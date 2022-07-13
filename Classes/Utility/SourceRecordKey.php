<?php
namespace JournalTransporterPlugin\Utility;

class SourceRecordKey
{
    /**
     * @param $id
     * @return string
     */
    static public function editor($id)
    {
        return self::user($id);
    }

    /**
     * @param $id
     * @return string
     */
    static public function reviewer($id)
    {
        return self::user($id);
    }

    /**
     * @param $id
     * @return string
     */
    static public function user($id)
    {
        return \User::class . ':' . $id;
    }

    /**
     * @param $id
     * @return string
     */
    static public function issue($id)
    {
        return \Issue::class . ':' . $id;
    }

    /**
     * @param $id
     * @return string
     */
    static public function section($id)
    {
        return \Section::class . ':' . $id;
    }

    /**
     * @param $id
     * @return string
     */
    static public function reviewForm($id)
    {
        return \ReviewForm::class . ':' . $id;
    }

    /**
     * @param $id
     * @return string
     */
    static public function reviewAssignment($id)
    {
        return \ReviewAssignment::class . ':' . $id;
    }

    /**
     * Not a real class in OJS
     * @param $id
     * @param $index
     * @return string
     */
    static public function reviewAssignmentResponse($id, $index)
    {
        return 'ReviewAssignmentResponse:' . $id . '-' . $index;
    }

    /**
     * Not a real class in OJS
     *
     * @param $articleId
     * @param $round
     *
     * @return string
     *
     * @psalm-param positive-int $round
     */
    static public function round($articleId, int $round)
    {
        return 'ReviewRound:' . $articleId . ':' . $round;
    }
}