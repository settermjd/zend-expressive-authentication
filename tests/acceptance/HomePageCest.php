<?php

use Mcustiel\Phiremock\Client\Phiremock;
use Mcustiel\Phiremock\Client\Utils\Is;
use Mcustiel\Phiremock\Client\Utils\Respond;
use Mcustiel\Phiremock\Client\Utils\A;

class HomePageCest
{
    const REMOTE_SERVICE_URL = 'remote.auth.service';
    const REMOTE_SERVICE_PORT = 18080;

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
        $formData = [
            'username' => 'me@example.com',
            'password' => 'my-password'
        ];

        $I->expectARequestToRemoteServiceWithAResponse(
            Phiremock::on(
                A::postRequest()
                    ->andUrl(Is::equalTo('/auth'))
                    ->andBody(Is::equalTo(http_build_query($formData)))
                    ->andHeader('Content-Type', Is::equalTo('application/x-www-form-urlencoded'))
            )->then(
                Respond::withStatusCode(200)
                    ->andBody(json_encode(['userId' => 10000]))
            )
        );

        $I->am('Guest User');
        $I->expectTo('be able to login and view the home page');
        $I->amOnPage('/');
        $I->seeInCurrentUrl('/login?redirect_to=' . urlencode('/'));
        $I->submitForm('#LoginUser', $formData);
        $I->seeCurrentUrlEquals('/');
    }


    public function testCannotLoginWithInvalidCredentials(AcceptanceTester $I)
    {
        $formData = [
            'username' => 'unknownuser',
            'password' => 'falsepassword'
        ];

        $I->expectARequestToRemoteServiceWithAResponse(
            Phiremock::on(
                A::postRequest()
                    ->andUrl(Is::equalTo('/auth'))
                    ->andBody(Is::equalTo(http_build_query($formData)))
                    ->andHeader('Content-Type', Is::equalTo('application/x-www-form-urlencoded'))
            )->then(
                Respond::withStatusCode(404)
                    ->andBody(json_encode(['userId' => 10000]))
            )
        );

        $I->am('Guest User');
        $I->expectTo('be able to login and view the home page');
        $I->amOnPage('/');
        $I->seeInCurrentUrl('/login?redirect_to=' . urlencode('/'));
        $I->submitForm('#LoginUser', $formData);
        $I->seeCurrentUrlEquals('/');
    }
}
