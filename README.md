# Entity Instantiator Generator Behavior for Propel

This free as in freedom behavior that easy up entity instantiation for your [propel](http://www.propelorm.org) query and object classes.

Thanks to the [ExtraPropertiesBehavior](https://github.com/Carpe-Hora/ExtraPropertiesBehavior) to act as such a great template.

The build status of the current master branch is tracked by Travis CI: 
[![Build Status](https://travis-ci.org/bazzline/php_propel_behavior_entity_instantiator.png?branch=master)](http://travis-ci.org/bazzline/php_propel_behavior_entity_instantiator)
[![Latest stable](https://img.shields.io/packagist/v/net_bazzline/php_propel_behavior_entity_instantiator.svg)](https://packagist.org/packages/net_bazzline/php_propel_behavior_entity_instantiator)

The scrutinizer status are:
[![code quality](https://scrutinizer-ci.com/g/bazzline/php_propel_behavior_entity_instantiator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bazzline/php_propel_behavior_entity_instantiator/) | [![build status](https://scrutinizer-ci.com/g/bazzline/php_propel_behavior_entity_instantiator/badges/build.png?b=master)](https://scrutinizer-ci.com/g/bazzline/php_propel_behavior_entity_instantiator/)

The versioneye status is:
[![dependencies](https://www.versioneye.com/user/projects/55e3222ffeb8cd1a50000958/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55e3222ffeb8cd1a50000958)

Downloads:
[![Downloads this Month](https://img.shields.io/packagist/dm/net_bazzline/php_propel_behavior_entity_instantiator.svg)](https://packagist.org/packages/net_bazzline/php_propel_behavior_entity_instantiator)

It is available at [openhub.net](https://openhub.net/p/php_propel_behavior_entity_instantiator).

If you want to control things in more detail, you should have a look to the [locator generator component for php](https://github.com/bazzline/php_component_locator_generator) and its [propel examples](https://github.com/bazzline/php_component_locator_generator/tree/master/example)

# Why

* no `new` in your code anymore
* no static `Query::create` call in your code anymore
* eases up writing test code (`createMyEntity` and `createMyEntityQuery` can be mocked)

# Usage

* make sure you have `extension=pdo_sqlite.so` enabled if you want to run phpunit
* the behavior creates a instantiator class and file
* the behavior has following parameters
    * `entity_add_to_entity_instantiator` - ("true"|"false") allowed to add or not ad this table to the instantiator, default is `true`
    * `entity_instantiator_class_name` - (string) that represents the class name, default is `DatabaseEntityInstantiator`
    * `entity_instantiator_extends` - (string) that represents the full qualified class name, default is ``
    * `entity_instantiator_indention`- (string) that represents the indention in the instantiator, default is `    `
    * `entity_instantiator_namespace`- (string) that represents the namespace in the instantiator, default is `` (no namespace)
    * `entity_instantiator_path_to_output`- (string) that represents the path (relative to `vendor/../`) where the instantiator file will be written, default is `data`
    * `entity_method_name_prefix`- (string) that represents the prefix for each instantiation method in the instantiator, default is `create<DatabaseName>` 

# Example 

Given a database with following settings

```xml
<database name="exampleDatabase">
    <behavior name="add_to_entity_instantiator">
        <parameter name="entity_instantiator_class_name" value="MyEntityInstantiator" />
        <parameter name="entity_instantiator_extends" value="MyAbstractEntityInstantiator" />
        <parameter name="entity_instantiator_indention" value="  " />
        <parameter name="entity_instantiator_namespace" value="Database\Service" />
        <parameter name="entity_instantiator_path_to_output" value="module/Database/src/Database/Service" />
        <parameter name="entity_method_name_prefix" value="create" />
    </behavior>
    
    <table name="user">
        <column name="id" type="INTEGER" primaryKey="true" autoincrement="true" />
        <column name="name" type="VARCHAR" size="255" />
        
        <!-- turn it on - default -->
        <behavior name="add_to_entity_instantiator">
            <parameter name="entity_add_to_entity_instantiator" value="true" />
        </behavior>
    </table>
    
    <table name="product">
        <column name="id" type="INTEGER" primaryKey="true" autoincrement="true" />
        <column name="name" type="VARCHAR" size="255" />
        
        <!-- turn it on -->
        <behavior name="add_to_entity_instantiator">
            <parameter name="entity_add_to_entity_instantiator" value="false" />
        </behavior>
    </table>
</database>
```

will create a file called `MyEntityInstantiator.php` in the path `module/Database/src/Database/Service` with following content.

```php
<?php

namespace Database\Service

/**
 * Class MyEntityInstantiator
 *
 * @author Net\Bazzline\Propel\Behavior\EntityInstantiatorNet\Bazzline\Propel\Behavior\EntityInstantiator\EntityInstantiatorGenerator
 * @since 2015-08-29
 * @see http://www.bazzline.net
 */
class MyEntityInstantiator extends MyAbstractEntityInstantiator
{
    /** 
     * @return PDO
     */
    public function getConnection($name = null, $mode = \Propel::CONNECTION_WRITE)
    {   
        return Propel::getConnection($name, $mode);
    }   

    /** 
     * @return \Database\User
     */
    public function createUser()
    {   
        return new \Database\User();
    }   

    /** 
     * @return \Database\UserQuery
     */
    public function createUserQuery()
    {   
        return new Database\UserQuery::create();
    }   
}   
```

# Installation

## By Hand

```
mkdir -p vendor/net_bazzline/php_propel_behavior_entity_instantiator
cd vendor/net_bazzline/php_propel_behavior_entity_instantiator
git clone https://github.com/bazzline/php_propel_behavior_entity_instantiator
```

## With [Packagist](https://packagist.org/packages/net_bazzline/php_propel_behavior_entity_instantiator)

```
"net_bazzline/php_propel_behavior_entity_instantiator": "dev-master"
```

## Enable Behavior in Propel

* add the following to your propel.ini
```
propel.behavior.create_entity_instantiator.class = lib.vendor.net_bazzline.php_propel_behavior_create_entity.source.AddToEntityInstantiatorBehavior
```

# API 

[API](http://bazzline.net/eb1538eb38f9635c0b1a1d47b020205681b7b569/index.html) available at [bazzline.net](http://www.bazzline.net)

# History

* upcoming
    * @todo
        * covered code with unit tests
        * use "use" instead and full qualified names usage in the methods
* [0.2.3](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.2.3) - released at 17.09.2015
    * fixed documentation issue
    * fixed phpdoc issue for generated method *getConnection*
    * phpdoc optimization when dealing with the `EntityCollection`
* [0.2.2](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.2.2) - released at 10.09.2015
    * refactored `EntityInstantiatorGenerator` internal code generation
* [0.2.1](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.2.1) - released at 09.09.2015
    * added "$name" and "$mode" as optional parameter for "getConnection()"
* [0.2.0](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.2.0) - released at 09.09.2015
    * fixed [issues/1](https://github.com/bazzline/php_propel_behavior_entity_instantiator/issues/1)
* [0.1.2](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.1.2) - released at 02.09.2015
    * changed default method prefix from "databaseName" to "createDatabaseName"
* [0.1.1](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.1.1) - released at 31.08.2015
    * added optional parameter `extends`
* [0.1.0](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.1.0) - released at 31.08.2015
    * initial release
