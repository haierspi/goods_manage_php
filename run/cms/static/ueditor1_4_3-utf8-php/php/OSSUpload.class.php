
<?Php
//引用oss
if (is_file('./aliyun-oss-php-sdk-2.6.0/autoload.php')) {
    require_once ('./aliyun-oss-php-sdk-2.6.0/autoload.php');
}


use OSS\OssClient;
use OSS\Core\OssException;
 
/**
 * Notes: 阿里云配置Ueditor上传
 * Created by assasin.
 * Date: 2019/12/27
 * Time: 15:53
 * Request-Method: POST+AES
 */
class OSSUpload
{
    public function __construct(){
 
    }
 
    /**
     * Notes: 阿里云配置Ueditor上传
     * Created by assasin.
     * Date: 2019/12/27
     * Time: 15:53
     * Request-Method: POST+AES
     */
    function uploadToAliOSS($file,$fullName){
        $accessKeyId = 'LTAI5tHu7skoU6xiTg3dt1Bg';//涉及到隐私就不放出来了
        $accessKeySecret = 'vLJVfims31pmMyZxhBv4tNyECZFbH4';//涉及到隐私就不放出来了
        $endpoint = 'oss-cn-shanghai.aliyuncs.com';//节点
        $bucket= 'start-assets-online';//" <您使用的Bucket名字，注意命名规范>";
        $object = $fullName;//" <您使用的Object名字，注意命名规范>";
        $accessHost = "https://assets.starfission.com";
        
 
        $content = $file["tmp_name"];//上传的文件
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->setTimeout(3600 /* seconds */);
            $ossClient->setConnectTimeout(10 /* seconds */);
            //$ossClient->putObject($bucket, $object, $content);
            // 先把本地的example.jpg上传到指定$bucket, 命名为$object
            $ossClient->uploadFile($bucket, $object, $content);

            $signedUrl = $ossClient->getUrl($bucket, $object);
            
            $path = explode('?',$signedUrl)[0];
            $obj['status'] = true;
            $obj['path'] = $accessHost.'/'.$path;
        } catch (OssException $e) {
            $obj['status'] = false;
            $obj['path'] = "";
            print $e->getMessage();
        }
        return $obj;
    }
}