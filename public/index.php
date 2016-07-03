<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    use RedBeanPHP\Facade as R;

    require '../vendor/autoload.php';
    
    R::setup( 'mysql:host=localhost;dbname=teknol', 'root', 'mysql' );
    
    $app = new \Slim\App;
    $app->add(new \CorsSlim\CorsSlim());
    
    $app->get('/', function( Request $request, Response $response ){

        $comments = R::find( 'contacts' );
    
        $response->withJson(
            $comments
        );
        return $reponse;
    });
    
    $app->post( '/', function( Request $request, Response $response ){
                
        $formData = $request->getParsedBody();
                
        R::begin();
        $success = array( "success"=>false, "message"=>"No fue posible guardar el comentario." );
        try{
            $contact = R::dispense('contacts');
            $contact->name = $formData['name'];
            $contact->email = $formData['email'];
            $contact->comment = $formData['comment'];
            
            R::store( $contact );
            R::commit();
            $success["success"] = true;
            $success["message"] = "Comentario guardado satisfactoriamente.";
        }
        catch(Exception $e) {
            $success['error'] = $e->getMessage();
            R::rollback();
        }
        
        $response->withJson( $success );
        
        return $response;
    } );
    
    $app->run();