# Welcome To Phalex

## Introduction

The name **Phalex** is a contraction of the words [Phal]con [Ex]tension.  
`Phalcon` is good, which is the fastest one PHP framework. However, when I develop web applications based on the Phalcon native, I realized most is the lambda functions. This is not bad, fast development, but in my opinion, it is somewhat difficult to maintain and manage code. So, I built this project aims to pack a few things, such as the application configurations, routing ...

Phalex bases on **Plug & Play Multi Modules** mechanism, so you can easy to develop multiple modules for your application. Something good in Phalex

- **Configuration** in Phalex application will be separated in each module. When runtime, Phalex auto merges all configurations into one, and you can easy to cache them.
- You **must not** set `view` service for each module, Phalex bases on DRY concept, so you just config **views_dir** for module, when runtime, Phalex auto set `view` service for yours.
- **Router** handler via configuration.
- **Auto load** for each module via configuration and you can call class cross modules.
- Default **Error Handler**, and you easy to extend or replace Phalex Error Handler
- Highly **Unit test** with [PHPUnit](https://phpunit.de/)
- And more ...
 
## System Requirements

- Require **PHP 5.5** or later. (We recommend using latest PHP version whenever possible)
- [Phalcon Framework 1.3](http://phalconphp.com/en/) or later (I don't test with Phalcon Framework 2.0, so I don't recommend use version 2.0 with Phalex)

