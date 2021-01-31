# in-words
PHP package to convert english numbers to other language number in words or months or week


### Package Installation

Require this package in your composer.json and update composer.

```bash
composer require himelali/in-words
```

#Example
```
$in_word = new InWord();

echo $in_word->engToBn(5207.56); //৫২০৭.৫৬

echo $in_word->numToWord(215245); //দুই লক্ষ পনের হাজার দুই শত পঁয়তাল্লিশ

echo $in_word->numToWord(527.56); //পাঁচ শত সাতাশ দশমিক পাঁচ ছয়
```