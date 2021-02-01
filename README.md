# in-words
PHP package to convert english numbers to other language number in words or months or week

Now included Bengali / (বাংলা) and English (ইংরেজি)

Default English active if you need bengali the pass parameter by constructor

For Bengali ```'bn'``` and for english it will be blank or ```'en'```
#### Version 1.0.2

## Example
```
$in_word = new InWord('bn');
```

### Package Installation

Require this package in your composer.json and update composer.

```bash
composer require himelali/in-words
```

## Example
```
$in_word = new InWord();

$in_word->setNumber(215245);

echo $in_word->getNumber(); //২১৫২৪৫

echo $in_word->getWord(); //দুই লক্ষ পনের হাজার দুই শত পঁয়তাল্লিশ


$in_word->setNumber(527.56);

echo $in_word->getNumber(); //৫২৭.৫৬

echo $in_word->getWord(); //পাঁচ শত সাতাশ দশমিক পাঁচ ছয়
```

#### Or use like:
```
$in_word = new InWord();

echo $in_word->setNumber(215245)->getNumber(); //৫২৭.৫৬

echo $in_word->setNumber(215245)->getWord(); //পাঁচ শত সাতাশ দশমিক পাঁচ ছয়
```