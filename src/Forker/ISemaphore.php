<?php
/**
 * Created by PhpStorm.
 * User: danibrutal
 * Date: 8/11/14
 * Time: 17:25
 */

namespace Forker;


interface ISemaphore {

    public function lock();

    public function unLock();

} 