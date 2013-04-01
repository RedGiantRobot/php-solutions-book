<?php
//get the contents to a variable
$contents = @ file_get_contents('/Users/Design1/CATMEDIA/testserver/root/git/php-solutions-book/site/private/filetest_01.txt');        

if ($contents === false) {
    echo 'Sorry, there was a problem with reading this file.';
} else {
    echo $contents;
    echo getFirstWords($contents, 7);
}

//split the contents into an array of words
//$words = explode(' ', $contents);

// extract the first four elements of the array
//$first = array_slice($words, 0, 4);

//join the the first four elements and display
//echo "\n\n" . implode(' ', $first);



//readfile('/Users/Design1/CATMEDIA/testserver/root/git/php-solutions-book/site/private/filetest_01.txt');

function getFirstWords($string, $number) {
    $words = explode(' ', $string);
    $first = array_slice($words, 0, $number);
    return implode(' ', $first);
}

?>