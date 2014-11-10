mjmChat is an extensions chat with nodeJs.
Requirements

To implement this extension, you need to have a dedicated IP for the web server port, you must assign the extension.

For more information about NodeJs can see the address. http://nodejs.org
Usage

After installation nodeJs on your server, you must file app.js in the extensions/mjmChat/SERVER command line will run as follows.

node app.js

The following code after the body-tag in your layout to run on all pages.

$this->widget('MjmChat', array(
                'title'=>'Chat room',
                'rooms'=>array('php'=>'PHP Room', 'html'=>'HTML Room'),
                'host'=>'http://localhost',
                'port'=>'3000',
            )
);

DEMO: http://yii.mjm3d.ir

You can change the extension of the partnership
