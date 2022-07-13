<?php
namespace JournalTransporterPlugin\Api\Journals\Articles\Digest;

use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Api\Response;
use JournalTransporterPlugin\Repository\Article;
use JournalTransporterPlugin\Repository\ArticleEmailLog;
use JournalTransporterPlugin\Repository\Journal;
use JournalTransporterPlugin\Utility\DataObject;
use JournalTransporterPlugin\Exception\InvalidRequestException;
use JournalTransporterPlugin\Utility\Date;

/**
 * Class Emails
 * @package JournalTransporterPlugin\Api\Journals\Articles\Digest
 */
class Emails extends ApiRoute
{
    protected Journal $journalRepository;
    protected Article $articleRepository;
    protected ArticleEmailLog $articleEmailLogRepository;

    /**
     * @param array $args
     *
     * @return Response|object[]
     *
     * @throws \Exception
     *
     * @psalm-return Response|list<object{ip:mixed, from:mixed, to:mixed, cc:mixed, bcc:mixed, subject:mixed, body:mixed, reference:string, datetime:string}>
     */
    public function execute(array $args)
    {
        // If we need this elsewhere, abstract it
        if (strlen($args['format']) > 0) {
            if (!in_array($args['format'], ['txt', 'json'])) {
                throw new InvalidRequestException("Format .{$args['format']} not allowed");
            } else {
                $format = $args['format'];
            }
        } else {
            $format = 'json';
        }

        $journal = $this->journalRepository->fetchOneById($args['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($args['article'], $journal);
        $resultSet = $this->articleEmailLogRepository->fetchByArticle($article);
        $articleEmailLogEntries = DataObject::resultSetToArray($resultSet);

        $emails = [];
        foreach ($articleEmailLogEntries as $articleEmailLogEntry) {
            $emails[] = (object)[
                'ip' => $articleEmailLogEntry->iPAddress,
                'from' => $articleEmailLogEntry->from,
                'to' => $articleEmailLogEntry->recipients,
                'cc' => $articleEmailLogEntry->ccs,
                'bcc' => $articleEmailLogEntry->bccs,
                'subject' => $articleEmailLogEntry->subject,
                'body' => $articleEmailLogEntry->body,
                'reference' => $articleEmailLogEntry->__class . ':' . $articleEmailLogEntry->id,
                'datetime' => Date::formatDateString($articleEmailLogEntry->dateSent)
            ];
        }

        usort(
            $emails,
            function ($a, $b) {
                return $a->datetime < $b->datetime ? -1 : 1;
            }
        );

        if ($format === 'json') {
            return $emails;
        } else {
            return $this->formatAsText($emails);
        }
    }

    /**
     * @param object[] $emails
     *
     * @return Response
     *
     * @psalm-param list<object{ip:mixed, from:mixed, to:mixed, cc:mixed, bcc:mixed, subject:mixed, body:mixed, reference:string, datetime:string}> $emails
     */
    protected function formatAsText(array $emails)
    {
        $digest = [];

        foreach ($emails as $email) {
            $digest[] = "
Sent At:   {$email->datetime}
Reference: {$email->reference}
From:      {$email->from} ({$email->ip})
To:        {$email->to}
Cc:        {$email->cc}
Bcc:       {$email->bcc}
Subject:   {$email->subject}

{$email->body}
";
        }
        $separator = "\n\n" . str_repeat('=', 80) . "\n\n";

        $string = wordwrap(implode($separator, $digest), 80);

        $response = new Response;
        $response->setContentType('text/plain');
        $response->setPayload($string);

        return $response;
    }
}