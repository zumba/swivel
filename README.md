# Zumba *Swivel*
***Swivel*** is a fresh spin on an old idea: [Feature Flags](http://en.wikipedia.org/wiki/Feature_toggle) (toggles, bits, switches, etc.).

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

***Swivel*** knows that the current user is in Bucket 6.  You have enabled the new Saucy formula for all users in Bucket 6, so ***Swivel*** executes `$this->getNewSaucyFormula()` and returns the result.

