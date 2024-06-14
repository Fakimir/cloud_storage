<?php
require_once 'Autoloader/MainAutoload.php';
require_once 'vendor/autoload.php';
//список uri и их методов запроса с ссылкой на сами методы
$urlList = [
    '/' => [
        'GET' => 'Home::list',
    ],

    '/user/' => [
        'GET' => 'UserController::getAll',
        'POST' => 'UserController::post',
        'PUT' => 'UserController::edit',
    ],
    '/^\/users\/(\d+)$/' => [
        'GET' => 'UserController::get',
        'DELETE' => 'UserController::delete',
    ],
    '/login' => [
        'POST' => 'AuthController::login',
    ],

    '/reset' => [
        'POST' => 'AuthController::resetPassword',
    ],

    '/logout' => [
        'GET' => 'AuthController::logout',
    ],

    '/admin/user/' => [
        'GET' => 'AdminController::getAll',
        'PUT' => 'AdminController::edit',
    ],

    '/^\/admin\/user\/(\d+)$/' => [
        'GET' => 'AdminController::get',
        'DELETE' => 'AdminController::delete',
    ],

    '/file/' => [
        'GET' => 'FilesController::getAll',
        'POST' => 'FilesController::post',
        'PUT' => 'FilesController::edit',
    ],

    '/^\/file\/(\d+)$/' => [
        'GET' => 'FilesController::get',
        'DELETE' => 'FilesController::delete',
    ],

    '/directory/' => [
        'PUT' => 'DirectoryController::edit',
        'POST' => 'DirectoryController::post',
    ],

    '/^\/directory\/(\d+)$/' => [
        'GET' => 'DirectoryController::get',
        'DELETE' => 'DirectoryController::delete',
    ],

    '/^\/files\/share\/\d+$/' => [
        'GET' => 'ShareController::fileGetShare',
    ],

    '/^\/files\/share\/\d+\/\d+$/' => [
        'POST' => 'ShareController::filePutShare',
        'DELETE' => 'ShareController::fileDeleteShare',
    ]
];

$regexKeys = [ //список uri с regex кодом для того чтобы удобнее прописывать подключение нужных скриптов и запуска методов
    '/^\/users\/(\d+)$/', '/^\/admin\/user\/(\d+)$/', '/^\/file\/(\d+)$/', '/^\/directory\/(\d+)$/', '/^\/files\/share\/\d+$/', '/^\/files\/share\/\d+\/\d+$/',
];

$request = new \Core\Classes\Request($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
$app = new \Core\Classes\App();

$tokenReader = new \Services\Classes\CookieTokenReader(); //получение токена
$tokenChecker = new \Services\Classes\JWTAccessTokenChecker('my_secret_key'); //проверка токена
$tokenDataExtractor = new \Services\Classes\TokenDataExtractor('my_secret_key'); //получить данные из токена

//Сервисы
$app->registerService('Auth', new \Services\Classes\Auth($tokenReader, $tokenChecker, $tokenDataExtractor)); //основной класс действий с токенами, обьеденияет TokenDataExtractor, JWTAccessTokenChecker и CookieTokenReader
$app->registerService('AuthChecker', new \Services\Classes\AuthChecker($app->getService('Auth'))); //тут уже на основе AuthChecker проверка авторизации и выдача доступа
$app->registerService('CreateResponse', new \Services\Classes\CreateResponse(new \Core\Classes\Response())); //вспомогательный класс который создает response
$app->registerService('AdminService', new \Services\Classes\AdminService()); //Сервис админа, пока что там только проверка на админа
$app->registerService('AddFileToServer', new \Services\Classes\AddFileToServer()); //класс для загрузки файла на сервер
$app->registerService('Server', new \Services\Classes\Server($app)); //сервис для изменения файла на сервисе удаление/редактирование
$app->registerService('UserListEmptyChecker', new \Services\Classes\UserListEmptyChecker());

//Репозитории
$app->registerRepository('UserRepository', new \Repositories\Classes\UserRepository(new \Core\Classes\App)); //репозиторий пользователя
$app->registerRepository('CheckExistanseRepository', new \Repositories\Classes\CheckExistanseRepository()); //проверка есть ли файл или директория в бд
$app->registerRepository('GetAccessibleFilesRepository', new \Repositories\Classes\GetAccessibleFilesRepository()); //получить все доступные файлы
$app->registerRepository('CheckAccessibilityRepository', new \Repositories\Classes\CheckAccessibilityRepository($app)); //проверка доступа к файлу/папке
$app->registerRepository('getAccessibleDirectories', new \Repositories\Classes\getAccessibleDirectories()); //получить все доступные директории

$app->registerRepository('AdminRepository', new \Repositories\Classes\AdminRepository()); //репозиторий админа

$app->registerRepository('FilesRepository', new \Repositories\Classes\FilesRepository($app)); //репозиторий для файлов
$app->registerRepository('DirectoryRepository', new \Repositories\Classes\DirectoryRepository($app)); //репозиторий для директорий

$app->registerRepository('AuthRepository', new \Repositories\Classes\AuthRepository()); //Тут лежат методы авторизации такие как login/resetPassword/logout

$app->registerRepository('ShareRepository', new \Repositories\Classes\ShareRepository()); //отдельный репозитория для "доступа" к файлам других пользователей

//Контроллеры
$app->registerController('UserController', new \Controllers\Classes\UserController($app)); //контроллер для сущности user
$app->registerController('FilesController', new \Controllers\Classes\FilesController($app)); //для файлов
$app->registerController('AuthController', new \Controllers\Classes\AuthController($app)); //контроллер для действий авторизации, вызывает AuthRepository
$app->registerController('DirectoryController', new \Controllers\Classes\DirectoryController($app)); //Контроллер для директорий
$app->registerController('AdminController', new \Controllers\Classes\AdminController($app)); //для админа
$app->registerController('ShareController', new \Controllers\Classes\ShareController($app)); //для доступа к файлам других пользователей
$app->registerController('Home', new \Controllers\Classes\Home());

//создание экземпляра класс Db для действий с бд
$db = \Core\Classes\Db::getInstance();
//получение url и метода из запроса
$url = $request->getRoute();
$method = $request->getMethod();

// Установка стратегии для find
if ((strpos($url, 'users') || preg_match('/^\/admin\/user\/(\d+)$/', $url)) && $method == 'GET') {
    $findStrategy = new \Core\Classes\FindById\FindUser();
}
else if (preg_match('/^\/file\/(\d+)$/', $url) && $method == 'GET') {
    $findStrategy = new \Core\Classes\FindById\FindFile();
}
else if (preg_match('/^\/directory\/(\d+)$/', $url) && $method == 'GET') {
    $findStrategy = new \Core\Classes\FindById\FindDirectory();
}
if (isset($findStrategy) && $findStrategy !== null) {
    $db->setFindStrategy($findStrategy);
}

//Усстановка стратегии для findAll
if (($url == '/user/' || $url == '/admin/user/') && $method == 'GET') {
    $findAllStrategy = new \Core\Classes\FindAll\FindAllUsers();
}
else if (($url == '/file/' || preg_match('/^\/file\/(\d+)$/', $url)) && ($method == 'GET' || $method == 'DELETE' || $method == 'PUT')) {
    $findAllStrategy = new \Core\Classes\FindAll\FindAllFiles();
}
if (isset($findAllStrategy) && $findAllStrategy !== null) {
    $db->setFindAllStrategy($findAllStrategy);
}

//вызов роутера и прлучение ответа
$router = new \Core\Classes\Router($app, $urlList, $regexKeys);
$response = $router->processRequest($request);

$data = $response->getData();
$headers = $response->getHeaders();

//обработка ответа
if (!empty($data) && isset($data) && !empty($headers) && isset($headers)) {
    foreach ($headers as $header) {
        header($header);
    }

    if ($data instanceof Exception || $data instanceof ErrorException || $data instanceof Error) {
        echo json_encode($data->getMessage());
        http_response_code($data->getCode());
    }
    else {
        echo json_encode($data);
    }
}