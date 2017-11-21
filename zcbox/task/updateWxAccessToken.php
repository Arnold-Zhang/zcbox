<?php
// 只在cli模式下运行
if(PHP_SAPI != 'cli') {
    exit;
}

require __DIR__ . '/../../../class/class_core.php';
require_once __DIR__ . '/../vendor/autoload.php';

$discuz = C::app();
$discuz->init();

$LOG_FILENAME = '_update_wx_access_token';

require_once __DIR__ . '/../lib/func.php';
require_once __DIR__ . '/../config/config.php';

LOG::DEBUG("update wx access token begin");

$rs = Weixin::_updateAccessToken();

for ($i=1; $i < 3; $i++) {
    if ($rs) {
        LOG::DEBUG("wx access token update success");
        break;
    }else{
        $rs = weAPI::_updateAccessToken();
        LOG::DEBUG("try update wx access token time:" . $i);
    }
}
LOG::DEBUG("update wx access token end");

?>
