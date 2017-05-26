<?php namespace Finetune\Finetune\Repositories\Tagging;

/**
 * Interface TaggingInterface
 * Interface for the tagging repo
 * @package Finetune\Finetune\Tagging
 */
interface TaggingInterface
{
    /**
     * Gets all tags from the database
     * @return mixed
     */
    public function getAll($site);

    /**
     * Takes the tag id and returns the a tag object
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * takes the tag string and returns a tag object
     * @param $tag
     * @return mixed
     */
    public function getTagFromTag($tag);

    /**
     * retrives an array from the database to generate a list for a select button
     * @return mixed
     */
    public function getTagList($site);

    /**
     * Only required parameter is $tags, gets all nodes from the tag, filter and limit it by the area id and limit
     * @param $tags
     * @param null $areaId
     * @param int $limit
     * @return mixed
     */
    public function getTagged($site, $tags, $areaId = null, $limit = 10);

    /**
     * Adds a tag to the datbase and clears the cache
     * @param $input
     * @return mixed
     */
    public function addTag($site, $input);

    /**
     * Updates a tag in the database
     * @param $id
     * @param $input
     * @return mixed
     */
    public function updateTag($site, $id, $input);

    /**
     * deletes a tag and all corresponding node linking
     * @param $id
     * @return mixed
     */
    public function deleteTag($id);


    /**
     * deletes a node from a tag
     * @param $tagId
     * @param $nodes
     * @return mixed
     */
    public function deleteTagNode($tagId, $nodes);

}