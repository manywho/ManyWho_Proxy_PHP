# ManyWho Proxy for PHP
This PHP page can be used to proxy the ManyWho API through your website. The benefit being that you can build HTML5 workflow applications using our API without worrying about cross site scripting issues. Make sure you check out our players project (https://github.com/manywho/ManyWho_HTML5_Players) as it contains the HTML5 source code for our drag-and-drop tooling, developer tooling and workflow runtime. We give you complete control so you can make ManyWho your own.

## Quick Setup
### ManyWho Proxy
Grab the PHP file from this repo and put it on your server. You need to make sure if you change the file name to something else or you put the file in a sub-directory, that you amend the path name accordingly:
....
$THIS_PATH_NAME = '/manywhoproxy.php';
....
 
### Player
From the players project (here: https://github.com/manywho/ManyWho_HTML5_Players) go to the "players" folder. These instructions assume you are wanting to host our drag-and-drop draw tool, though they also apply to the other tools more broadly. In terms of the pages, this is how they are broken out:

1. Build.htm: The player for the developer tooling (Our instance here: https://flow.manywho.com/build)
2. Draw.htm: The player for the drag-and-drop tooling (Our instance here: https://flow.manywho.com/draw)
3. Default.htm: The default player for running a workflow application (you can view this by clicking on "Run" or "Activate" from within our drag-and-drop tooling)
4. Translate.htm: The player for the translation/internationalization tooling (Our instance here: https://flow.manywho.com/translate)
5. Twilio.htm: A beta version of our tooling, altered to work specifically with Twilio (http://twilio.com)

Assuming you have the draw.htm file open, you'll need to override the BASE_PATH_URL value to the location of the proxy PHP file on your server:
....
ManyWhoConstants.BASE_PATH_URL = 'http://manywho.com/manywhoproxy.php';
....

## What's NOT working 
A few things we know that don't work:

1. Linking to the other build tools
2. Loading of players (you need to host these somewhere on your service)
3. PUT/DELETE API requests - just not handled in the PHP proxy currently
