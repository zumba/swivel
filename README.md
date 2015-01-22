# Zumba *Swivel*

[![Build Status](https://travis-ci.org/zumba/swivel.svg?branch=master)](https://travis-ci.org/zumba/swivel)
[![Coverage Status](https://coveralls.io/repos/zumba/swivel/badge.svg)](https://coveralls.io/r/zumba/swivel)

***Swivel*** is a fresh spin on an old idea: [Feature Flags](http://en.wikipedia.org/wiki/Feature_toggle) (toggles, bits, switches, etc.).

**Important:** This software is still under initial development.  Not all features are complete, and the API may change.

Typical Feature Flags are *all* or *nothing*: either the feature is on for everyone, or it is off for everyone.

```php
// Old School Feature Flag

if ($flagIsOn) {
    // Do something new
} else {
    // Do something old
}
```

Typical Feature Flags are based on boolean conditionals with few abstractions (if this, then that).  Although powerful in their simplicity, this typically leads to increased [cyclomatic complexity](http://en.wikipedia.org/wiki/Cyclomatic_complexity) and eventual technical debt.

## Swivel is Different
***Swivel*** is fundamentally different from Typical Feature Flags in two ways:

* Features can be enabled for a subset of an application's users.
* Features are not simple conditionals; a developer defines one or more strategies (behaviors) and ***Swivel*** takes care of determining which strategy to use.

### Buckets
With ***Swivel***, users are seperated into one of ten "buckets," allowing a feature to be enabled for a subset of users.  The advantages of this approach are clear:

* Deploying a new feature to 10% of users enables developers to catch unforseen bugs/problems with new code without negatively affecting all users.  These kind of deployments are called [Canary Releases](http://martinfowler.com/bliki/CanaryRelease.html).  As soon as it is determined that new code is safe, roll out the new feature to more users in increments (30%, 50%, etc.); eventually the feature can enabled for all users, safely.
* A/B testing becomes a breeze.  Imagine running up to 9 versions of a new feature with one group kept in reserve as a control.  Is feature "A" negatively affecting revenue metrics for 10% of your users? No problem: turn it off and go with version "B" instead.  This is easy to do with ***Swivel***.

### Behaviors
Agile code needs to be simple and easy to change.  Typical Feature Flags allow developers to quickly iterate when business rules change or new features are implemented, but this can often lead to complex, under engineered, brittle blocks of code.

***Swivel*** encourages the developer to implement changes to business logic as independent, high level *strategies* rather than simple, low level deviations.

## Example: Quick Look

```php
$formula = $swivel->forFeature('AwesomeSauce')
    ->addBehavior('formulaSpicy', [$this, 'getNewSpicyFormula'])
    ->addBehavior('formulaSaucy', [$this, 'getNewSaucyFormula'])
    ->defaultBehavior([$this, 'getFormula'])
    ->execute();
```

# Getting Started

The first thing you'll want to do is generate a random number between 1 and 10 for each user in your application. This will be the user's "bucket."

**Note:** As a best practice, once a user is assigned to a bucket they should remain in that bucket forever. You'll want to store this value in a session or cookie like you would other basic user info.

Next, you'll need to create a map of features to buckets that should have each feature enabled.  Here is an example of a simple feature map:

```php
$map = [
    // This is a parent feature slug.
    // It is enabled for users in buckets 4, 5, and 6
    'Feature' => [4,5,6],

    // This is a behavior slug.  It is a subset of the parent slug,
    // and it is only enabled for users in buckets 4 and 5
    'Feature.Test' => [4, 5],

    // Behavior slugs can be infinitely nested.
    // This one is only enabled for users in bucket 5.
    'Feature.Test.VersionA' => [5]
];
```

When your application is bootstrapping, configure ***Swivel*** and create a new manager instance:

```php
// Get this value from the session or persistent storage.
$userBucket = 5; // $_SESSION['bucket'];

// get the map from the DB
$featureMap = [ 'Feature' => [4,5,6], 'Feature.Test' => [4,5] ];

$config = new \Zumba\Swivel\Config($featureMap, $userBucket);
$swivel = new \Zumba\Swivel\Manager($config);
```

Way to go!  ***Swivel*** is now ready to use.
