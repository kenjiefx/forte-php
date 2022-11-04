# forte-php
Imports your private reposotiry as dependency in Composer

![](https://cdn.shopify.com/s/files/1/0560/7466/6159/files/forte-composer.png?v=1667575312)

Wanted to bring in your private repository into the party? Let Forte import it for you. This package offers an alternative to paying Private Packagist, yet makes sure that when you are ready to pay someday, migrating your dependencies will not be so hard!

### Installation
```composer install forte-php```

### How It Works 
There is nothing really magical happening under the hood. Forte PHP uses the power of GitHub API to connect to your private repository. 

### Setup 
Create your `my.forte.json` file at the root of your project:
```
{
    "username":"YOUR_GITHUB_USERNAME",
    "token":"YOUR_GITHUB_PERSONAL_ACCESS_TOKEN"
}
```
**👉 IMPORTANT: MAKE SURE TO ADD `my.forte.json` TO YOUR `.gitignore` LIST**

## How to add your private repo 
1. Your private repository must contain `composer.json`
2. Create your forte.composer.json, like so: 
```
{
    "require": {
        "kenjiefx/jwt":{
            "branch":"0.1.0",
            "repository":"KenjieFx/JWT"
        }
    }
}
```
2. The `kenjiefx/jwt` field in the example above refers to name field in your repository's composer.json, like so: 

![](https://cdn.shopify.com/s/files/1/0560/7466/6159/files/examplesetup.jpg?v=1667577130)

## Installing your private repo 
To install your private repository, run `php forte install` 

## Autoloading your private repo 
Run `composer dump-autoload`
