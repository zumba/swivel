# Zumba *Swivel*
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

***Swivel*** knows that the current user is in Bucket 6.  You have enabled the new Saucy formula for all users in Bucket 6, so ***Swivel*** executes `$this->getNewSaucyFormula()` and returns the result.

## Moving Away From Feature Flags
The best way to visualize ***Swivel***'s benefits is to look at an example where Traditional Feature Flags cause more harm than good.  Here's a fictional case study.  Let's assume the following:

You maintain an E-commerce application that sells the best instant waffle mix, bar none. [:+1:](https://imaginepepperland.files.wordpress.com/2012/11/oh-please-tell-me-more.jpg)

One morning your boss gives you a feature request to change the primary payment provider for the shop.  This new provider could save the business a few bucks per transaction, so it's high priority.  The code changes are simple enough, but they shouldn't go live for about a month.

You change the code to look like this:

```php
// Traditional approach
public function makePayment($userId, $total) {
    if (FeatureFlag::enabled('PaymentProvider.Thrifty')) {
        $this->setPaymentProvider('Thrifty');
    } else {
        $this->setPaymentProvider('OldSchool');
    }
    $this->paymentProvider->pay($userId, $total);
}
```

A week later the Marketing Manager tells you that the OldSchool provider gives recurring customers a ton of incentives to keep buying the same online instant waffle mix every time.  You have many recurring customers.  The savings incurred by using the Thrifty provider may not justify the possible loss of revenue from these repeat customers shopping elsewhere.  So, to be safe you change the code to look like this:

```php
// Traditional approach plus iteration!
public function makePayment($userId, $total) {
    $thriftyEnabled = FeatureFlag::enabled('PaymentProvider.Thrifty');
    if (!$this->isRecurring($userId) && $thriftyEnabled) {
        $this->setPaymentProvider('Thrifty');
    } else {
        $this->setPaymentProvider('OldSchool');
    }
    $this->paymentProvider->pay($userId, $total);
}
```

A week before launch day the Business Analyst asks you to add some analytics to the payment code.  He wants to know the totals when a user pays with the OldSchool provider to see if they spend less when using Thrifty.


```php
// Traditional approach is starting to get complicated.
public function makePayment($userId, $total) {
    $thriftyEnabled = FeatureFlag::enabled('PaymentProvider.Thrifty');
    if (!$this->isRecurring($userId) && $thriftyEnabled) {
        if (FeatureFlag::enabled('PaymentProvider.NewAnalytics')) {
            $this->report('Thrifty', $total);
        }
        $this->setPaymentProvider('Thrifty');
    } else {
        if (FeatureFlag::enabled('PaymentProvider.NewAnalytics')) {
            $this->report('OldSchool', $total);
        }
        $this->setPaymentProvider('OldSchool');
    }
    $this->paymentProvider->pay($userId, $total);
}
```
