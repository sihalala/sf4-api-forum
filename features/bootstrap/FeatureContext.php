<?php


class FeatureContext extends \Behatch\Context\RestContext
{
    const USERS = [
        'admin' => 'secret123#'
    ];
    const AUTH_URL = '/api/login_check';
    const AUTH_JSON = ' 
        { 
            "username": "%s", 
            "password": "%s" 
        } 
    ';
    private $em;
    private $fixtures;
    private $matcher;
    public function __construct(\Behatch\HttpCall\Request $request,
                                \App\DataFixtures\AppFixtures $fixtures,
                                \Doctrine\ORM\EntityManagerInterface $em )
    {
        parent::__construct($request);
        $this->fixtures = $fixtures;
        $this->em = $em;
        $this->matcher =
            (new \Coduo\PHPMatcher\Factory\SimpleFactory())->createMatcher();
    }

    /**
     * @BeforeScenario @createSchema
     */
    public function createSchema()
    {
        // Get entity metadata
        $classes = $this->em->getMetadataFactory()
            ->getAllMetadata();

        // Drop and create schema
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);

        // Load fixtures... and execute
        $purger = new \Doctrine\Common\DataFixtures\Purger\ORMPurger($this->em);
        $fixturesExecutor =
            new \Doctrine\Common\DataFixtures\Executor\ORMExecutor(
                $this->em,
                $purger
            );

        $fixturesExecutor->execute([
            $this->fixtures
        ]);
    }



    // cai nay la dung de khai bao cho 1 cau moi
    /**
     * @Given I am authenticated with :user
     */
    public function iAmAuthenticatedAs($user)
    {
        $this->request->setHttpHeader('Content-Type', 'application/ld+json');
        $this->request->send(
            'POST',
            $this->locatePath(self::AUTH_URL),
            [],
            [],
            sprintf(self::AUTH_JSON, $user, self::USERS[$user])
        );


        $json = json_decode($this->request->getContent(), true);
        // Make sure the token was returned
        $this->assertTrue(isset($json['token']));

        $token = $json['token'];

        $this->request->setHttpHeader(
            'Authorization',
            'Bearer '.$token
        );
    }

    /**
     * @Then the JSON matches expected template
     */
    public function theJsonMatchesExpectedTemplate(\Behat\Gherkin\Node\PyStringNode $json)
    {
        $actual = $this->request->getContent();
        var_dump($actual);
        var_dump($json->getRaw());
        $this->assertTrue(
            $this->matcher->match($actual, $json->getRaw())
        );
    }

    /**
     * @BeforeScenario @image
     */
    public function prepareImages()
    {

    }

}
