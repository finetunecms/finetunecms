<?php
namespace Finetune\Finetune\Services\Tagging;

use Finetune\Finetune\Repositories\Tagging\TaggingInterface;

/**
 * Class TaggingService
 * @package Services\Snippet
 */
class TaggingService
{
    /**
     * @var TaggingInterface
     */
    protected $taggingRepo;


    /**
     * @param TaggingInterface $taggingRepo
     */
    public function __construct(TaggingInterface $taggingRepo)
    {
        $this->taggingRepo = $taggingRepo;
    }

    public function getAll($site){
        return $this->taggingRepo->getAll($site);
    }

    /**
     * tag can be an array of tags, this can either be a mixture of tag and id or just tags / id
     * limit limits the amount of results
     * area is the areaid of the tagged nodes
     * Returns tagged nodes by the filters applied
     * @param $tag
     * @param int $limit
     * @param null $area
     * @return mixed
     */
    public function getTaggedNodes($tag, $area = null, $limit = null)
    {
        return $this->taggingRepo->getTagged($tag, $area, $limit);
    }

    /**
     * tag can be an array of tags, this can either be a mixture of tag and id or just tags / id
     * limit limits the amount of results
     * area is the areaid of the tagged nodes
     * Returns tagged nodes randomised by the filters
     * @param $tag
     * @param int $limit
     * @param null $area
     * @return mixed
     */
    public function randomTaggedNodes($tag, $limit = 10, $area = null)
    {
        $nodes = $this->taggingRepo->getTagged($tag, $area, $limit);
        if (count($nodes) > $limit) {
            $randomNodes = $nodes->random($limit);
        } else {
            $randomNodes = $nodes;
        }
        return $randomNodes;
    }

}