# in-words
PHP package to convert english numbers to other language number in words or months or week

Now included Bengali / (বাংলা) and English (ইংরেজি)

Version 1.0.0

### Package Installation

Require this package in your composer.json and update composer.

```bash
composer require himelali/in-words
```

#Example
```
$in_word = new InWord();

$in_word->setNumber(215245);

echo $in_word->getNumber(); //২১৫২৪৫

echo $in_word->getWord(); //দুই লক্ষ পনের হাজার দুই শত পঁয়তাল্লিশ


$in_word->setNumber(527.56);

echo $in_word->getNumber(); ; //৫২০৭.৫৬

echo $in_word->getWord(); //পাঁচ শত সাতাশ দশমিক পাঁচ ছয়
```