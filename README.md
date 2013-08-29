Illarra Core Bundle
===================

[![Build Status](https://secure.travis-ci.org/illarra/IllarraCoreBundle.png)](http://travis-ci.org/illarra/IllarraCoreBundle) [![Total Downloads](https://poser.pugx.org/illarra/core-bundle/d/total.png)](https://packagist.org/packages/illarra/core-bundle) [![Latest Stable Version](https://poser.pugx.org/illarra/core-bundle/version.png)](https://packagist.org/packages/illarra/core-bundle) [![Latest Unstable Version](https://poser.pugx.org/illarra/core-bundle/v/unstable.png)](https://packagist.org/packages/illarra/core-bundle)

Installation
------------

Add this bundle (and its dependencies, if they are not already there) to your application's kernel:

```php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new JMS\AopBundle\JMSAopBundle(),
        new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
        new Knp\Bundle\MenuBundle\KnpMenuBundle(),
        new Lexik\Bundle\FormFilterBundle\LexikFormFilterBundle(),
        new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
        new Illarra\CoreBundle\IllarraCoreBundle(),
        // ...
    );
}
```
