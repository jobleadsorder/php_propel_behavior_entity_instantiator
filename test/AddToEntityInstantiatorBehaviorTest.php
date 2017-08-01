<?php
use Net\Bazzline\Propel\Behavior\EntityInstantiator\Manager;
use org\bovigo\vfs\vfsStream;

/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-08-02
 */
class AddToEntityInstantiatorBehaviorTest extends PHPUnit_Framework_TestCase
{
    /** @var string */
    private $connectionMode;

    /** @var string */
    private $connectionName;

    /** @var string */
    private $extends;

    /** @var string */
    private $indention;

    /** @var string */
    private $maximumClassName;

    /** @var string */
    private $minimumClassName;

    /** @var string */
    private $namespace;

    /** @var string */
    private $path;

    /** @var string */
    private $prefix;

    /**
     * @todo create two EntityInstantiator classes, one with all values and one with minimum needed values to validate default values
     */
    protected function setUp()
    {
        //begin of setting runtime environments
        $fileSystem = vfsStream::setup();

        $this->connectionMode   = 'Propel::CONNECTION_READ';
        $this->connectionName   = 'my_default_connection_name';
        $this->extends          = '\stdClass';
        $this->indention        = '  ';
        $this->maximumClassName = 'ExampleMaximumInstantiator';
        $this->minimumClassName = 'ExampleMinimumInstantiator';
        $this->namespace        = 'Test\Net\Bazzline\Propel';
        $this->path             = $fileSystem->url();
        $this->prefix           = 'create';
        //end of setting runtime environments

        $buildIsNeeded = (
            (!class_exists('MaximumTableOne'))
            || (!class_exists('MaximumTableTwo'))
            || (!class_exists('MinimumTableOne'))
            || (!class_exists('MinimumTableTwo'))
        );

        if ($buildIsNeeded) {
            $schemaWithMaximumValues     = <<<EOF
<database name="example_database" defaultIdMethod="native">
    <behavior name="add_to_entity_instantiator">
        <parameter name="entity_instantiator_class_name" value="$this->maximumClassName" />
        <parameter name="entity_instantiator_extends" value="$this->extends" />
        <parameter name="entity_instantiator_indention" value="$this->indention" />
        <parameter name="entity_instantiator_namespace" value="$this->namespace" />
        <parameter name="entity_instantiator_path_to_output" value="$this->path" />
        <parameter name="entity_instantiator_method_name_prefix" value="$this->prefix" />
        <parameter name="entity_instantiator_add_to_entity_instantiator" value="true" />
        <parameter name="entity_instantiator_default_connection_mode" value="$this->connectionMode" />
        <parameter name="entity_instantiator_default_connection_name" value="$this->connectionName" />
    </behavior>

    <table name="maximum_table_one">
        <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />
    </table>

    <table name="maximum_table_two">
        <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />

        <behavior name="add_to_entity_instantiator">
            <parameter name="entity_instantiator_add_to_entity_instantiator" value="false" />
        </behavior>
    </table>
</database>
EOF;

            $schemaWithMinimumValues     = <<<EOF
<database name="example_database" defaultIdMethod="native">
    <behavior name="add_to_entity_instantiator">
        <parameter name="entity_instantiator_class_name" value="$this->minimumClassName" />
        <parameter name="entity_instantiator_extends" value="$this->extends" />
        <parameter name="entity_instantiator_indention" value="$this->indention" />
        <parameter name="entity_instantiator_namespace" value="$this->namespace" />
        <parameter name="entity_instantiator_path_to_output" value="$this->path" />
        <parameter name="entity_instantiator_method_name_prefix" value="$this->prefix" />
        <parameter name="entity_instantiator_add_to_entity_instantiator" value="true" />
    </behavior>

    <table name="minimum_table_one">
        <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />
    </table>

    <table name="minimum_table_two">
        <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />

        <behavior name="add_to_entity_instantiator">
            <parameter name="entity_instantiator_add_to_entity_instantiator" value="false" />
        </behavior>
    </table>
</database>
EOF;

            $builder        = new PropelQuickBuilder();
            $configuration  = $builder->getConfig();
            $configuration->setBuildProperty('behavior.add_to_entity_instantiator.class', __DIR__ . '/../source/AddToEntityInstantiatorBehavior');
            $builder->setConfig($configuration);
            $builder->setSchema($schemaWithMaximumValues);

            $builder->build();
            //we have to call generate manually since it is called only when php execution is finished
            Manager::getInstance()->generate();

            $builder        = new PropelQuickBuilder();
            $configuration  = $builder->getConfig();
            $configuration->setBuildProperty('behavior.add_to_entity_instantiator.class', __DIR__ . '/../source/AddToEntityInstantiatorBehavior');
            $builder->setConfig($configuration);
            $builder->setSchema($schemaWithMinimumValues);

            $builder->build();
            //we have to call generate manually since it is called only when php execution is finished
            Manager::getInstance()->generate();
        }
    }

    public function testInstantiatorFileExists()
    {
        $path = $this->path . DIRECTORY_SEPARATOR . $this->maximumClassName . '.php';
        $this->assertTrue(file_exists($path));

        require_once ($path);

echo(__METHOD__ . PHP_EOL . file_get_contents($path));
        $path = $this->path . DIRECTORY_SEPARATOR . $this->minimumClassName . '.php';
        $this->assertTrue(file_exists($path));

echo(__METHOD__ . PHP_EOL . file_get_contents($path));
        require_once ($path);
    }

    /**
     * @depends testInstantiatorFileExists
     */
    public function testInstantiatorClassExists()
    {
        $fullQualifiedClassName = $this->namespace . '\\' . $this->maximumClassName;
        $this->assertTrue(class_exists($fullQualifiedClassName));

        $fullQualifiedClassName = $this->namespace . '\\' . $this->minimumClassName;
        $this->assertTrue(class_exists($fullQualifiedClassName));
    }

    /**
     * @depends testInstantiatorClassExists
     */
    public function testInstantiatorExtendsStdClass()
    {
        $fullQualifiedClassName = $this->namespace . '\\' . $this->maximumClassName;
        $instantiator           = new $fullQualifiedClassName();

        $this->assertInstanceOf('stdClass', $instantiator);

        $fullQualifiedClassName = $this->namespace . '\\' . $this->minimumClassName;
        $instantiator           = new $fullQualifiedClassName();

        $this->assertInstanceOf('stdClass', $instantiator);
    }

    /**
     * @depends testInstantiatorClassExists
     */
    public function testInstantiatorClassHasExpectedMethods()
    {
        $fullQualifiedClassName = $this->namespace . '\\' . $this->maximumClassName;

        $methods = get_class_methods($fullQualifiedClassName);

        $this->assertContains('getConnection', $methods);
        $this->assertContains('createMaximumTableOne', $methods);
        $this->assertContains('createMaximumTableOneQuery', $methods);

        $fullQualifiedClassName = $this->namespace . '\\' . $this->minimumClassName;

        $methods = get_class_methods($fullQualifiedClassName);

        $this->assertContains('getConnection', $methods);
        $this->assertContains('createMinimumTableOne', $methods);
        $this->assertContains('createMinimumTableOneQuery', $methods);
    }

    /**
     * @depends testInstantiatorClassHasExpectedMethods
     */
    public function testThatMethodsReturningRightInstances()
    {
        $fullQualifiedClassName = $this->namespace . '\\' . $this->maximumClassName;
        $instantiator           = new $fullQualifiedClassName();

        $this->assertTrue(($instantiator->createTableOne() instanceof MaximumTableOne));
        $this->assertTrue(($instantiator->createTableOneQuery() instanceof MaximumTableOneQuery));

        $fullQualifiedClassName = $this->namespace . '\\' . $this->minimumClassName;
        $instantiator           = new $fullQualifiedClassName();

        $this->assertTrue(($instantiator->createTableOne() instanceof MinimumTableOne));
        $this->assertTrue(($instantiator->createTableOneQuery() instanceof MinimumTableOneQuery));
    }
}
