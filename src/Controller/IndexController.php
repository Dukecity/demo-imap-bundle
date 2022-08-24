<?php

namespace App\Controller;

use SecIT\ImapBundle\Service\Imap;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/', 'index')]
    public function index(Imap $imap): Response
    {
        // testing with a full error message
        try {
            $isConnectable = $imap->testConnection('example_connection', true);

            return new Response("SUCCESS");
        #} catch (\Exception $exception) {
        }catch(\PhpImap\Exceptions\ConnectionException $ex) {

            echo "IMAP connection failed: " . implode(",", $ex->getErrors('all'));
            #dump($exception->getMessage());
            return new Response("FAILED");
        }
    }
}
