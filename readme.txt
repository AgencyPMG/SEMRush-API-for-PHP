SEM Rush API

This software is licensed under the MIT License

The MIT License (MIT)
Copyright (c) 2012 Performance Media Group <mail@pmg.co>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.


How to use

1) Setup your SEMRush API Key, you may generate one [here](http://www.semrush.com/api.html), you must set the API Key in the SemRush.php file

protected $apikey='XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

2) include the file, and call one of reporting functions, you can also use the callReport function to give other export columns determined in the [SEMRush API] (http://www.semrush.com/api.html)

include_once('SemRush.php');

$s = new SemRush('http://pmg.co');
echo "Main Report: ";
print_r($s->getMainReport());

//you can also use the SEMRush PHP API to call different types of reports not listed
print_r($s->callReport("domain_rank", array("export_columns"=>"Dn,Rk,Or,Ot,Oc,Ad,At,Ac")));

