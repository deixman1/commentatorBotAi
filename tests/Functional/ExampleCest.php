<?php


namespace Functional;

use Codeception\Util\HttpCode;
use Tests\Support\FunctionalTester;

/**
 * Для функциональных тестов подключены модули Laravel, REST. Подробнее по ссылкам
 * @link https://codeception.com/docs/FunctionalTests
 * @link https://github.com/Codeception/laravel-module-tests
 * @link https://codeception.com/docs/APITesting
 */
class ExampleCest
{
    public function _before(FunctionalTester $I)
    {
    }

    // tests
    public function tryToTest(FunctionalTester $I)
    {
        $I->sendGet('/');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see("Hello! It's the service!");
    }
}
