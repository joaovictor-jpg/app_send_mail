<?php

    require_once './bibliotecas/validacao_email.php';
    require_once './bibliotecas/PHPMailer/Exception.php';
    require_once './bibliotecas/PHPMailer/OAuth.php';
    require_once './bibliotecas/PHPMailer/PHPMailer.php';
    require_once './bibliotecas/PHPMailer/POP3.php';
    require_once './bibliotecas/PHPMailer/SMTP.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    class Mensagem {
        private $para = null;
        private $assunto = null;
        private $mensagem = null;
        public $status = array('codigo_status' => null, 'descricao_status' => '');

        public function __set($attr, $valor) {
            $this->$attr = $valor;
        }

        public function __get($attr) {
            return $this->$attr;
        }

        public function mensagemValida(){
            if(empty($this->para) ||  empty($this->assunto) || empty($this->mensagem)){
                return false;
            }
            return true;
        }
    }

    $mensagem = new Mensagem();

    $analise = mail_parse_address($_POST['para']);

    if(!$analise) {

        header('Location: index.php?loge=erro');

    } else {
        foreach($_POST as $attr => $valor){
            $mensagem->__set($attr, $valor);
        }

        if(!$mensagem->mensagemValida()) {
            echo 'Mensagem não é valida';
            header('Location: index.php');

        }
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = false;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = '@gmail.com';                     //SMTP username
            $mail->Password   = '';                               //SMTP password
            $mail->SMTPSecure = 'tls';                                  //Enable implicit TLS encryption
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('xmodgamesxmodgames6@gmail.com', 'anonymous');
            $mail->addAddress($mensagem->__get('para'));     //Add a recipient
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $mensagem->__get('assunto');
            $mail->Body    = $mensagem->__get('mensagem');
            $mail->AltBody = 'É necessário usar um client que suporte HTML para ter acesso total ao conteudo dessa mensagem.';

            $mail->send();
            $mensagem->status['codigo_status'] = 1;
            $mensagem->status['descricao_status'] = 'E-Mail enviado com sucesso';

        } catch (Exception $e) {
            $mensagem->status['codigo_status'] = 2;
            $mensagem->status['descricao_status'] = 'Não foi possível enviar este e-mail! Por favor tente novamente mais tarde. Detalhes do erro: ' . $mail->ErrorInfo;
        }
    }
?>

<!DOCTYPE html>
<html lang="pr-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Mail Send</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div class="container">
            <div class="py-3 text-center">
				<img class="d-block mx-auto mb-2" src="logo.png" alt="" width="72" height="72">
				<h2>Send Mail</h2>
				<p class="lead">Seu app de envio de e-mails particular!</p>
			</div>
            <div class="row">
                <div class="col-md-12">
                    <? if($mensagem->status['codigo_status'] == 1) { ?>
                        <div class="container">
                            <h1 class="display-4 text-success">Sucesso</h1>
                            <p><? echo $mensagem->status['descricao_status']?></p>
                            <a href="index.php" class="btn btn-success btn-lg mt-5 text-white">Voltar</a>
                        </div>
                    <? } ?>

                    <? if($mensagem->status['codigo_status'] == 2) { ?>
                        <div class="container">
                            <h1 class="display-4 text-danger">Ops!</h1>
                            <p><? echo $mensagem->status['descricao_status']?></p>
                            <a href="index.php" class="btn btn-success btn-lg mt-5 text-white">Voltar</a>
                        </div>
                    <? } ?>
                </div>
            </div>
    </div>
</body>
</html>