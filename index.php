<!DOCTYPE html>

<?php
  if(!isset($_GET['action']) || $_GET['action'] != "process"){
    $formPost = false;
  }
  if(isset($_GET['action']) && $_GET['action'] == "process"){
    $formPost = true;
    $allEmails = $_POST['emails'];

    //Explode by newline
    $emails = explode("\r\n", $allEmails);
    //remove empty values
    $emails = array_filter($emails);
    $totalEmails = count($emails);

    $validAddresses = array();
    $invalidAddresses = array();
    $noMXRecord = array();

    if($emails){
      foreach($emails as $email){

          $email = trim($email);
          $email = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $email);

          if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            //endereço é válido, verificar MX record
            //extrair domínio
            $domain = substr(strrchr($email, "@"), 1);
            if (!checkdnsrr($domain, 'MX')) {
                array_push($noMXRecord, $email);
            }else{
              //valid address, valid MX record
              array_push($validAddresses, $email);
            }
          }else{
            //Email inválido
            array_push($invalidAddresses, $email); 
          }
      }

      $nValidAddresses = count($validAddresses);
      $nInvalidAddresses = count($invalidAddresses);
      $nNoMXRecord = count($noMXRecord);

    }else { //empty textarea
      $nValidAddresses = 0;
      $nInvalidAddresses = 0;
    }

  }
?>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Email Validator</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Living.Web - Ponto.C</a>
        </div>
        <div class="collapse navbar-collapse">
          
        </div><!--/.nav-collapse -->
      </div>
    </div>

    <div class="container">

      <div class="starter-template">
        <span height="100">&nbsp;<br/><br/></span>
        <h1>Email Validator</h1>
        <p class="lead">Please insert your email addresses on the textarea below, one per line.</p>

        <form method="POST" action="index.php?action=process" name="emailValidator">
          <textarea class="form-control" rows="5" name="emails"><?php if(isset($_POST['emails'])){echo trim($_POST['emails']);}?></textarea>
          <br/>
          <button type="submit" class="btn btn-default">Process</button>
        </form>

        


        <?php
          if($formPost)
          {
        ?>
          <h1>Results:</h1>
          <table class="table">
            <tr>
              <td colspan="3">Number of processed addresses: <?php echo $totalEmails; ?></td>
            </tr>
            <tr>
              <td>
                <table width="100%">
                  <tr>
                    <td valign="top">
                      <table class="table">
                        <tr>
                          <td>
                            Valid addresses (<?php echo $nValidAddresses; ?>)
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <textarea class="form-control" rows="10"><?php
                              foreach($validAddresses as $address){
                                echo $address."\r\n";
                              }
                            ?></textarea>

                          </td>
                        </tr>
                      </table>
                    </td>
                    <td valign="top">
                      <table class="table">
                        <tr>
                          <td>
                            Invalid addresses (<?php echo $nInvalidAddresses; ?>)
                          </td>
                        </tr>
                        <tr>
                          <td>
                            
                            <textarea class="form-control" rows="10"><?php
                              foreach($invalidAddresses as $address){
                                echo $address."\r\n";
                              }
                            ?></textarea>

                          </td>
                        </tr>
                      </table>
                    </td>
                    <td valign="top">
                      <table class="table">
                        <tr>
                          <td>
                            No MX Record (<?php echo $nNoMXRecord; ?>)
                          </td>
                        </tr>
                        <tr>
                          <td>
                            
                            <textarea class="form-control" rows="10"><?php
                              foreach($noMXRecord as $address){
                                echo $address."\r\n";
                              }
                            ?></textarea>

                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        <?php
          }
        ?>






      </div>

    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
