#GamersNet (The Project) README:

/*
First: Note that "GamersNet" is not (and probably it won't be) the final project name!
So, is better call it just "The Project"
*/

##Documentation:
- Git:
    - Main page: http://git-scm.com/
    - Tutorial: http://www.vogella.com/articles/Git/article.html

- PHP:
- Manual and tutorials (english & spanish): http://www.php.net/manual/es/
- Performance tips and more (may be a little bit old, but worth reading it): http://phplens.com/lens/php-book/optimizing-debugging-php.php

- HTML: http://www.w3schools.com/html/default.asp

- CSS: http://www.w3schools.com/css/default.asp

- Ajax:
    - Definition: http://es.wikipedia.org/wiki/AJAX
    - AJAX Tutorial (with Javascript): http://www.librosweb.es/ajax/capitulo1.html
    
- MySQL: http://dev.mysql.com/doc/refman/5.0/es/index.html

- Javascript and NodeJS:
    - Javascript intro: http://www.w3schools.com/js/
    - Javascript tutorial (with AJAX): http://www.librosweb.es/ajax/capitulo1.html
    - Object orientation and inheritance in Javascript (worth reading it a lot): http://manuel.kiessling.net/2012/03/23/object-orientation-and-inheritance-in-javascript-a-comprehensive-explanation/
    - NodeJS official webpage: http://nodejs.org/
    - NodeJS intro and tutroial: http://www.nodebeginner.org
    - JQuery (Javascript): http://jquery.com/

- Last but not least: http://google.com

##PHP coding standards:
###Variable names:
Normal variables must be named following the next scheme:
>*$myVariableName (Not $MyVariableName or $myvariablename or $Myvariablename or $my_variable_name)*

Private data members must be preceded by a "_":
>*$_myPrivateVariable*

Constants: All contants must be named using capital letters and using "_" as word separator:
>*$MY_CONSTANT, $MY_CONSTANT_ARRAY['INDEX'] (Not $My_Constant or $MY_CONSTANT_ARRAY['index'])*

**All private class variables must be accessed using the specific Set() and Get() methods.** The __set() and __get()built-in methods can be used, but only if the variable accesed has no relation with a database field or doesn't need any kind of check.

###if () ... else/*if ()*/ ... blocks:

    ```php
    // The "(" must be separated from the "if" by a space:
    if ($myVar > MY_CONSTANT_LIMIT)
    {
        // My sentences
    }
    else
    {
        // My alternative sentences
    }

    // The "{}" could be omitted if only one sentece is going to be executed:
    if ($myVar > MY_CONSTANT_LIMIT)
        // My sentence
    else
        // My other sentence

    // Or for example:
    if ($myVar > MY_CONSTANT_LIMIT)
    {
        // My sentences
    }
    else
        // My other sentence

    // If there is only one sentence, but it is a composed one like a loop, the "{}" are a must:
    if ($myVar > MY_CONSTANT_LIMIT)
    {
        while ($myControl < MY_CONTROL_LIMIT)
        {
            // My sentences
        }
    }
    else
    {
        for($i = 0; $i < MY_CONTROL_LIMIT2; ++$i)
            // My sentence
    }
    ```

###Functions:
All functions must be commented before the declaration following the next scheme:

    ```php
    /**
     * Function description.
     * @param var_type $varName Variable description.
     * @return var_type Variable description.
     */
     ```

Function names should follow the next scheme: no "_", each word starts with capital letter. For example:
>*MyFunction(), MyOtherFunction() (not myFunction() or myotherfunction() or my_function())*

Function declarations should follow the next scheme: no space between the function name and the params section, the opening bracket "{" must be in the next line. For example:

    ```php
    function MyFunction($myParam, $myOtherParam)
    {
        return ($myParam + $myOtherParam);
    }
    ```

Full example:

    ```php
    /**
     * Adds two numbers if both are positive.
     * @param integer $oneNumber One of the numbers that must be added
     * @param integer $otherNumber The other number
     * @return mixed Returns the sum of both numbers, or false if one (or both) of the numbers is negative.
     */
    function AddTwoNumbers($oneNumber, $otherNumber)
    {
        if ($oneNumber < 0 || $otherNumber < 0)
            return fals;
        return ($oneNumber + $otherNumber);
    }
    ```

To be continued...
