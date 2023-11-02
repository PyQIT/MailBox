<!DOCTYPE html>
<html>
<head>
    <title>
        Zaliczenie na 10.12.2019
    </title>
</head>
<body>
    Zaliczenie na 10.12.2019 <br>
<?php

    $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
    $username = 'projektpai2019@gmail.com';
    $password = 'Projekt_PAI2019';

    $inbox = imap_open($hostname,$username,$password) 
            or die('Connection problem, error: ' . imap_last_error());

    echo "Connected with Gmail IMAP Server<br><hr>";

    $emails = imap_search($inbox,'ALL');

    if($emails) {
        rsort($emails);
        $downloadTmp = 2;

        foreach($emails as $email_number){
            $overview = imap_fetch_overview($inbox,$email_number,0);
            $message = imap_fetchbody($inbox,$email_number,'1.1');
            $structure = imap_fetchstructure($inbox, $email_number);
            $attachments = array();

            foreach ($overview as $view) {
                echo "From: {$view->from}
                 To: {$view->to}
                 Subject: {$view->subject}
                 Size: {$view->size} bytes ";
            }

            if(isset($structure->parts) && count($structure->parts)){
                for($i = 0; $i < count($structure->parts); $i++){

                    $attachments[$i] = array(
                    'filename' => '',
                    'attachment' => ''
                     );

                    if($structure->parts[$i]->ifdparameters) {
                        foreach($structure->parts[$i]->dparameters as $object){
                            if(strtolower($object->attribute) == 'filename'){
                                echo "Text: ".$message;
                                $attachments[$i]['filename'] = $object->value;
                                echo "Attachment: ". $object -> value;
                                echo "<form method='post'>
                                      <button type='submit' name='downloadAttachment'
                                       value='".$object -> value."'>
                                       Download </button>".
                                      "</form>";
                                echo "<hr>";
                            }
                        }
                    }

                    
                    $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);

                    if($structure->parts[$i]->encoding == 3)
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                }
            }

            if(isset($_POST['downloadAttachment'])) { 
                $filename = $attachments[1]['filename'];
                $tmp = $_POST['downloadAttachment'];
                if(!empty($filename)){
                    if($attachments[1]['filename'] === $tmp){
                        $fp = fopen("./" . $filename, "w+");
                        fwrite($fp, $attachments[1]['attachment']);
                        fclose($fp);
                    }
                }
            }
        }
    } 

    imap_close($inbox);
?>
<br><br><br><hr><br>
<marquee behavior='scroll' direction='right'>3ID12B<marquee>
</head>
</html>
