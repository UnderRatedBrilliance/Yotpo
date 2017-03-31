<?php
/**
 * Stellaron_${PACKAGE_NAME} extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Stellaron
 * @package        Stellaron_${PACKAGE_NAME}
 * @copyright      Copyright (c) 2017
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         georg <george@agenaastro.com>
 */

namespace Urb\Yotpo;


use AstronomyConnect\ItemHub\Review\Models\Review;

class YotpoLatestReviews
{
    protected $yotpo;

    protected $reviewModel;

    public function __construct()
    {
        $this->yotpo = new Yotpo(config('yotpo'));
        $this->reviewModel = app(Review::class);
    }



    public function getReviews()
    {
        return $this->yotpo->getReviews([
            'count' => 200,
            'since_date' => ($this->getLastImportedReview()) ? (string)$this->getLastImportedReview()->created_at->addSeconds(1) : '2016-11-21',
        ])['reviews'];
    }
    public function getLastImportedReview()
    {
        return $this->reviewModel
            ->withTrashed()
            ->where('private_notes','like','%yotpo%')
            ->orderBy('created_at','desc')
            ->first();
    }
}
