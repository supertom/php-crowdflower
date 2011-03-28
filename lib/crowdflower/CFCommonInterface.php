<?php
/* 
 *
 */

/**
 *
 * @author supertom
 */
interface CFCommonInterface {
    public function get(/* int */ $id=0);
    public function create(/* array */ $data);
    public function update(/* int */ $id, /* array */ $data);
    public function delete(/* int */ $id);

}
?>
