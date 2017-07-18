<?php
/**
 * Custom Deploying Script
 * @author Arnaud NICOLAS <arno06@gmail.com>
 * @version 1.0
 */

/**
 * Default git projects path
 */
$deploy_dir = '/home/sites/arnaud-nicolas.fr/projects/';

/**
 * Specific projects
 */
$available = array('php-fw'=>'../');

/**
 * List all git projects available
 */
if ($handle = opendir($deploy_dir))
{
    while (false !== ($entry = readdir($handle)))
    {
        if($entry == "." || $entry == "..")
            continue;
        $available[$entry] = '';
    }
    closedir($handle);
}

/**
 * Handling deploying task
 */
if(isset($_GET['project']) && !empty($_GET['project']))
{
    $result = array();
    /**
     * If available, reset then pull nothing more
     */
    if(isset($available[$_GET['project']]))
    {
        $b = 'cd '.$deploy_dir.$available[$_GET['project']].$_GET['project'].' && git reset --hard HEAD && git pull';
        exec($b, $return);
        $result = array("message"=>$b, 'result'=>$return);
	}
	else
	{
        //probably not a good request
        $result = array("message"=>"Nop :)", 'result'=>false);
	}
    header("Content-Type:application/json");
    echo json_encode($result);
    exit(0);
}

?>
<html>
    <head>
        <title>Deploy - arnaud-nicolas.fr</title>
        <script src="//dependencies.arnaud-nicolas.fr/?need=Request"></script>
        <link href="//fonts.googleapis.com/css?family=Asap:400,700,400italic,700italic" rel="stylesheet">
        <link href="assets/style.css" rel="stylesheet">
        <script>

            function init()
            {
                document.querySelector('form').addEventListener('submit', submitHandler, false);
            }

            function submitHandler(pEvent)
            {
                pEvent.preventDefault();
                document.querySelector('input[type="submit"]').classList.add('loading');
                Request.load('index.php?project='+document.querySelector('select').value)
                    .onComplete(function(pResponse)
                    {
                        var info = document.querySelector('.info');
                        document.querySelector('input[type="submit"]').classList.remove('loading');
                        info.innerHTML = pResponse.responseJSON.result.join("<br/>");
                        info.classList.remove('hidden');

                        setTimeout(function(){
                            info.classList.add('hidden');
                        }, 5000);
                    });
            }

            window.addEventListener("DOMContentLoaded", init, false);
        </script>
    </head>
    <body>
        <header>
            <div><a href=""><span class="square"><span>+</span></span>Deploy</a></div>
        </header>
        <div class="info hidden">woot</div>
        <div class="container">
            <form action="index.php">
                <select name="project" id="project_select">
                    <?php
                    foreach($available as $n=>$path)
                    {
                        ?>
                        <option value="<?php echo $n; ?>"><?php echo $n; ?></option>
                        <?php
                    }
                    ?>
                </select>
                <input type="submit" value="Reset & Pull">
            </form>
        </div>
    </body>
</html>

