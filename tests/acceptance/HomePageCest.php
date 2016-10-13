<?php

class HomePageCest
{
    public function testRedirectedToLoginIfNotLoggedInOnGetRequest(AcceptanceTester $I)
    {
        $I->am('Guest User');
        $I->expectTo('be redirected to the /login route when viewing the home page');
        $I->amOnPage('/');
        $I->seeInCurrentUrl('/login');
    }

    public function testNotRedirectedIfOnLoginPageWhenUnauthenticated(AcceptanceTester $I)
    {
        $I->am('Guest User');
        $I->expectTo('not be redirected, if requesting the login page, when not authenticated');
        $I->amOnPage('/login');
        $I->seeInCurrentUrl('/login');
    }

    public function testCanLoginAndViewTheHomePage(AcceptanceTester $I)
    {
        $I->am('Guest User');
        $I->expectTo('be able to login and view the home page');
        $I->amOnPage('/');
        $I->seeInCurrentUrl('/login?redirect_to=' . urlencode('/'));
        $I->submitForm('#LoginUser', [
            'username' => 'me@example.com',
            'password' => 12345
        ]);
        $I->seeCurrentUrlEquals('/');
    }
}
