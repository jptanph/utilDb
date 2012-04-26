<?php
class utilCacheDebug extends utilDebugAbstract
{
    public function start()
    {
        parent::start();
    }

    function utilDebugModel($debug, $debugType, $className)
    {
        $this->isActive         = $debug;
        $this->debugType    = $debugType;
        $this->className    = $className;
    }

    public function setLog($log)
    {
        if(!DEBUG) {
            return;
        }
 
        parent::setLog($log);
        $this->aLogs[] = $log.' ['.$this->useTime().']';

        commDebug::setCacheClass(array($log, $this->useTime()));
    }
}