<?php

namespace models;

use libs\Model;
use Emarref\Jwt\Claim;

class UserModel extends Model
{
    protected $key = '1810B';
    /**
     * @content 通过用户名获取用户的信息
     */
    public function getUserInfoByUserName($username)
    {
        return $this -> query('select * from __table__ where username=?',[$username]);
    }
    /**
     * @content 通过令牌获取用户的信息
     */
    public function getUserInfoByToken($token)
    {
        return $this -> query('select * from __table__ where user_token=?',[$token]);
    }
    /**
     * @content 存储token到数据库
     */
    public function saveToken($user_token,$expire_time,$where)
    {
        return $this -> exec('update __table__ set user_token=?,expire_time=? where username=?',[$user_token,$expire_time,$where]);
    }
    /**
     * @content 通过id获取用户的数据
     */
    protected function getUserInfoByUid($id) {

		return $this -> query("select * from __table__ where id=? limit 1",[$id]);

	}
    /**
     * @content 创建token
     */
    public function createToken($id)
    {
        $token = new \Emarref\Jwt\Token();

        // Standard claims are supported
        $token -> addClaim(new Claim\Audience(['audience_1', 'audience_2']));
        $token -> addClaim(new Claim\Expiration(new \DateTime('30 minutes')));
        $token -> addClaim(new Claim\IssuedAt(new \DateTime('now')));
        $token -> addClaim(new Claim\Issuer('yeyunyang'));
        $token -> addClaim(new Claim\JwtId('1'));
        $token -> addClaim(new Claim\NotBefore(new \DateTime('now')));
        $token -> addClaim(new Claim\Subject('http://www.api.com'));
        $token -> addClaim(new Claim\PrivateClaim('id', $id));
        
        $jwt = new \Emarref\Jwt\Jwt();
		$algorithm = new \Emarref\Jwt\Algorithm\Hs256($this->key);
		$encryption = \Emarref\Jwt\Encryption\Factory::create($algorithm);
        $serializedToken = $jwt -> serialize($token, $encryption);
        return $serializedToken;
    }
    /**
     * @content 验证token
     */
    public function verifyToken($token='')
    {
        $jwt = new \Emarref\Jwt\Jwt();
		$token = $jwt -> deserialize($token);
		// 实例化上下文
		$algorithm = new \Emarref\Jwt\Algorithm\Hs256($this->key);
		$encryption = \Emarref\Jwt\Encryption\Factory::create($algorithm);

		$context = new \Emarref\Jwt\Verification\Context($encryption);
		$context -> setAudience('audience_1');
		$context -> setIssuer('yeyunyang');
		$context -> setSubject('http://www.api.com');

		try {
		    $jwt -> verify($token, $context);
		} catch (\Emarref\Jwt\Exception\VerificationException $e) {
		    return $e -> getMessage();
        }
        // dd($token);
		// 如果验证通过，返回用户的id
		$id = $token -> getPayload() -> findClaimByName('id') -> getValue();
		// 返回用户信息
		return $this -> getUserInfoByUid($id) ?? null;
    }
}