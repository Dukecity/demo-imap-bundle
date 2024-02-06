<?php

namespace App\Controller;

use PhpImap\Exceptions\ConnectionException;
use PhpImap\Mailbox;
use SecIT\ImapBundle\Connection\ConnectionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @throws \Exception
     * @var ConnectionInterface[] $connections
     */
    #[Route('/', 'index')]
    public function index(#[TaggedIterator('secit.imap.connection')] iterable $connections): Response
    {
        // testing with a full error message
        try {

            #/* @var ConnectionInterface[] $connections */
            foreach ($connections as $connection)
            {
                $mailbox = $connection->getMailbox();

                dump($mailbox);
                $isConnectable = $connection->testConnection(true);
            }

            return new Response("SUCCESS");
        #} catch (\Exception $exception) {
        }catch(\PhpImap\Exceptions\ConnectionException $ex) {

            echo "IMAP connection failed: " . implode(",", $ex->getErrors('all'));
            #dump($exception->getMessage());
            return new Response("FAILED");
        }
    }

    /**
     * @throws \Exception
     * @see https://127.0.0.1:8000/read/INBOX
     */
    #[Route('/read/{shortPath?}', 'read')]
    public function read(#[Target('itSharedTestConnection')]
                         ConnectionInterface $connection, ?string $shortPath="INBOX"): Response
    {
        // testing with a full error message
        try {
            $mailbox = $connection->getMailbox();

            #dump($mailbox->checkMailbox());

            #dump($mailbox->getMailboxes()); # Alle "Ordner" auflisten.

            #$mailbox->countMails();
            #dump($mailbox->getImapPath());
            #$mailbox->getMail()
            #dump($mailbox->getImapStream());
            #dump($mailbox->getListingFolders('*'));

            #dump($mailbox->getMailboxInfo());

            #dump("Quota ".$mailbox->getQuotaUsage(). ' from '. $mailbox->getQuotaLimit()); # keine Rechte


            # see https://github.com/barbushin/php-imap/blob/master/examples/get_and_parse_all_emails_with_matching_subject.php
            # https://www.php.net/imap_search
            #$mail_ids = $mailbox->searchMailbox('SUBJECT "Test"');
            $mail_ids = $mailbox->searchMailbox('NEW');
            #$mail_ids = $mailbox->searchMailbox('UNSEEN');# https://docs.microsoft.com/en-us/outlook/troubleshoot/user-interface/can't-search-additional-mailbox
            #$mail_ids = $mailbox->searchMailbox('ALL');
            var_dump($mail_ids);

/*
            // If you don't need to grab attachments you can significantly increase performance of your application
            $mailbox->setAttachmentsIgnore(true);
            #$folders = $mailbox->getMailboxes('*'); // get the list of folders/mailboxes
            $folders = $mailbox->getMailboxes('INBOX'); // get the list of folders/mailboxes

            // loop through mailboxes
            foreach($folders as $folder) {

                // switch to particular mailbox
                $mailbox->switchMailbox($folder['fullpath']);

                // search in particular mailbox
                #$mails_ids[$folder['fullpath']] = $mailbox->searchMailbox('SINCE "1 Jan 2024" BEFORE "28 Jan 2024"');
                $mails_ids[$folder['fullpath']] = $mailbox->searchMailbox('ALL');
            }

            var_dump($mails_ids);*/



            foreach ($mail_ids as $mail_id)
            {
                echo "+------ P A R S I N G ------+\n";

                $email = $mailbox->getMail(
                    $mail_id, // ID of the email, you want to get
                    false // Do NOT mark emails as seen (optional)
                );

                echo 'from-name: ' . ($email->fromName ?? $email->fromAddress) . "\n";
                echo 'from-email: ' . $email->fromAddress . "\n";
                echo 'to: ' . $email->toString . "\n";
                echo 'subject: ' . $email->subject . "\n";
                echo 'message_id: ' . $email->messageId . "\n";

                echo 'mail has attachments? ';
                if ($email->hasAttachments()) {
                    echo "Yes\n";
                } else {
                    echo "No\n";
                }

                if (!empty($email->getAttachments())) {
                    echo \count($email->getAttachments()) . " attachements\n";
                }
                if ($email->textHtml) {
                    echo "Message HTML:\n" . $email->textHtml;
                } else {
                    echo "Message Plain:\n" . $email->textPlain;
                }

                if (!empty($email->autoSubmitted)) {
                    // Mark email as "read" / "seen"
                    $mailbox->markMailAsRead($mail_id);
                    echo "+------ IGNORING: Auto-Reply ------+\n";
                }

                if (!empty($email_content->precedence)) {
                    // Mark email as "read" / "seen"
                    $mailbox->markMailAsRead($mail_id);
                    echo "+------ IGNORING: Non-Delivery Report/Receipt ------+\n";
                }
            }

            $mailbox->disconnect();

            return new Response("SUCCESS");
         }
         catch (ConnectionException $ex) {
                exit('IMAP connection failed: '.$ex->getMessage());
            }
        catch(\Exception $e) {

            #echo "IMAP connection failed: " . implode(",", $e->getErrors('all'));
            #dump($exception->getMessage());

            var_dump('An error occured: '.$e->getMessage());
            var_dump('An error occured: '.$e->getTraceAsString());

            return new Response("FAILED");
        }
    }
}
