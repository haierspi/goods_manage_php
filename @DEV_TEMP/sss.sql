

INSERT INTO starfission.pre_goods (
				goods_id,
				`goods_name`,
				`goods_price`,
				`goods_market_price`,
				goods_url,
				goods_type,
				goods_stock,
				goods_total_stock,
				buy_num_limit,
				goods_title_pic,
				goods_thumb_pic,
				goods_image,
				goods_ar,
				goods_ar_image,
				goods_body,
				goods_body_mobile,
				`status`,
				blockchain_address,
				contract_network,
				contract_tokenuri_url_domain,
				contract_tokenuri_url_pre,
				contract_keystore_path,
				created_at,
				updated_at
				
)

SELECT
		id as goods_id,
		`goods_name`,
		`goods_price`,
		`goods_market_price`,
		goods_sn as goods_url,
		1,
		goods_stock,
		total_goods_stock as goods_total_stock,
		purchase_limit as buy_num_limit,
		goods_title_pic,
		goods_cover as goods_thumb_pic,
		goods_cover as goods_image,
		'' as goods_ar_image,
		goods_image_3d as  goods_ar,
		 ifnull(goods_body,''),
		goods_body_mb as goods_body_mobile,
		`status`,
		blockchain_hash as blockchain_address,
		network as contract_network,
		tokenuri_url_domain as contract_tokenuri_url_domain,
		tokenuri_url_pre as contract_tokenuri_url_pre,
		keystore_path as contract_keystore_path,
		created_at,
		updated_at

FROM
		old.goods;







INSERT INTO starfission.pre_wallet_goods (
  id,
  uid,
  nickname,

  transaction_hash,
  contract_hash,
  wallet_hash,


  goods_id,
  goods_name,
  goods_thumb_pic,
  goods_title_pic,
  goods_image,
  goods_ar,
  goods_ar_image,


  brand_id,
  brand_name,
  release_id,
  release_name,
  copyright_id,
  copyright_name,

  blockchain_id,
  blockchain_name,
  blockchain_key,
  blockchain_icon,

  contract_metadata_name,
  contract_metadata_description,
  contract_metadata_image,
  contract_metadata_animation_url,

  updated_at,
  created_at
				
)

SELECT
  id,
  uid,
  nickname,
 
  transaction_hash,
  contract_hash,
  wallet_hash,

  goodsid as goods_id,
  '' as goods_name,
   ifnull(goods_cover,'') as goods_thumb_pic,
  ifnull(goods_cover,'') as goods_title_pic,
  ifnull(goods_cover,'') as goods_image,
  goods_vr as goods_ar,
  goods_image_3d asgoods_ar_image,


  brand_id,
  brand_name,
  release_id,
  release_name,
  copyright_owner_id as copyright_id,
  copyright_owner_name as copyright_name,


  '1' as blockchain_id,
  chain_name as blockchain_name,
  'conflux' as blockchain_key,
  chain_image as blockchain_icon,

  metadata_name as contract_metadata_name,
  metadata_description as contract_metadata_description,
  metadata_image as contract_metadata_image,
  metadata_animation_url as contract_metadata_animation_url,

  updated_at,
  created_at
FROM
		old.pre_wallet_goods;



UPDATE pre_wallet_goods SET goods_title_pic= replace(goods_title_pic, 'https://xinglie.oss-cn-shanghai.aliyuncs.com', 'https://assets.starfission.com');
UPDATE pre_wallet_goods SET goods_thumb_pic= replace(goods_thumb_pic, 'https://xinglie.oss-cn-shanghai.aliyuncs.com', 'https://assets.starfission.com');
UPDATE pre_wallet_goods SET goods_image= replace(goods_image, 'https://xinglie.oss-cn-shanghai.aliyuncs.com', 'https://assets.starfission.com');
UPDATE pre_wallet_goods SET goods_ar= replace(goods_ar, 'https://xinglie.oss-cn-shanghai.aliyuncs.com', 'https://assets.starfission.com');


UPDATE pre_order SET goods_title_pic= replace(goods_title_pic, 'https://xinglie.oss-cn-shanghai.aliyuncs.com', 'https://assets.starfission.com');
UPDATE pre_order SET goods_thumb_pic= replace(goods_thumb_pic, 'https://xinglie.oss-cn-shanghai.aliyuncs.com', 'https://assets.starfission.com');
UPDATE pre_order SET goods_image= replace(goods_image, 'https://xinglie.oss-cn-shanghai.aliyuncs.com', 'https://assets.starfission.com');
UPDATE pre_order SET goods_ar= replace(goods_ar, 'https://xinglie.oss-cn-shanghai.aliyuncs.com', 'https://assets.starfission.com');





UPDATE pre_wallet_goods SET goods_ar= replace(goods_ar, 'https://xinglie.oss-cn-shanghai.aliyuncs.com', 'https://assets.starfission.com');


UPDATE pre_contract_metadata SET contract_metadata_image= replace(contract_metadata_image, 'https://xinglie.oss-cn-shanghai.aliyuncs.com', 'https://assets.starfission.com');



SELECT * FROM `xinglie_prod`.`users` WHERE `type` = '1' ORDER BY `id` DESC LIMIT 0,1000

  `goods_title_pic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品标题图',
  `goods_thumb_pic` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '商品缩略图',
  `goods_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品封面图',


  

  

INSERT INTO starfission.pre_member (
  uid,
  avatar,
  nickname,
  mobile,
  created_at,
  updated_at
)

SELECT
  id as uid,
  'https://assets.starfission.com/images/avatars/1.png',
  name as   nickname,
  mobile,
  created_at,
  updated_at
FROM old.users WHERE `type` = 1;



INSERT INTO starfission.pre_order (
  order_id,
  order_sn,
  uid,
  nickname,
  goods_id,
  goods_name,
  goods_type,
  goods_buy_num,
  goods_price,
  goods_total_cost,
  payment,
  payment_type,
  payment_cost,
  payment_datetime,
  payment_pay_transaction_id,
  payment_data,
  link_phone,
  link_man,
  is_shiped,
  status,
  created_at,
  updated_at,
  deleted_at
)

SELECT
  orderid as order_id,
  ordersn as order_sn,
  uid,
  nickname,
  goodsid as goods_id,
  goods_name,
  '1' as goods_type,
  goods_buy_num,
  goods_price,
  goods_total_cost,
  payment,
  payment_type,
  payment_cost,
  payment_datetime,
  payment_pay_transaction_id,
  payment_data,
  link_phone,
  link_man,
  is_shiped,
  status,
  created_at,
  updated_at,
  deleted_at
FROM old.pre_order;




INSERT INTO starfission.pre_wallet (
  uid,
  eth_address,
  cfx_main_address,
  cfx_test_address,
  private_key,
  updated_at,
  created_at,
  deleted_at
)

SELECT
  uid,
  eth_address,
  cfx_main_address,
  cfx_test_address,
  private_key,
  updated_at,
  created_at,
  deleted_at
FROM old.pre_wallet;




UPDATE pre_member SET avatar= replace(avatar, 'https://xinglie.oss-cn-shanghai.aliyuncs.com', 'https://assets.starfission.com');




INSERT INTO starfission.pre_contract_metadata (
  contract_metadata_name,
  contract_metadata_description,
  contract_metadata_image,
  goods_id

)

SELECT
  goods_name as contract_metadata_name,
  copyright_name as contract_metadata_description,
  goods_image as contract_metadata_image,
  goods_id
FROM starfission.pre_goods;

UPDATE
 
    pre_goods g
SET
    contract_metadata_id = (
        SELECT
            contract_metadata_id
        FROM
            pre_contract_metadata m
        WHERE
            g.goods_id = s.goods_id
    )



UPDATE
  pre_order o
SET
    goods_url = (
        SELECT
            goods_url
        FROM
            pre_goods g
        WHERE
            g.goods_id = o.goods_id
    )



UPDATE
  pre_goods g
SET
    goods_url = (
        SELECT
            concat(goods_url,'/') as goods_url
        FROM
            pre_goods g
        WHERE
            g.goods_id = o.goods_id
    )

  where  g.goods_id >=20

 
SELECT
	g.goods_id,
	c.goods_id AS goods_id_c,
	g.goods_id <=> c.goods_id AS 'ID',
	g.contract_metadata_id,
	c.contract_metadata_id AS contract_metadata_id_c,
	g.contract_metadata_id <=> c.contract_metadata_id AS 'mid' 
FROM
	pre_goods g
	LEFT JOIN pre_contract_metadata c ON g.contract_metadata_id = c.contract_metadata_id;



SELECT
	g.goods_id,
	c.goods_id AS goods_id_c,
	g.goods_id <=> c.goods_id AS 'ID',
	g.contract_metadata_id,
	c.contract_metadata_id AS contract_metadata_id_c,
	g.contract_metadata_id <=> c.contract_metadata_id AS 'mid' 
FROM
	pre_goods g
	LEFT JOIN pre_contract_metadata c ON g.contract_metadata_id = c.contract_metadata_id;



UPDATE
  pre_goods g
SET
  contract_tokenuri_url_pre = (
      SELECT
          concat(goods_url,'/') as goods_url
      FROM
          pre_goods g2
      WHERE
          g2.goods_id = g.goods_id
  )
WHERE  g.goods_id >=13

UPDATE
  `pre_order` o
SET
  `goods_title_pic` = (
    SELECT
      goods_title_pic
    FROM
      pre_goods g
    WHERE
      o.goods_id = g.goods_id
  )
where
  o.goods_title_pic = ''



	
UPDATE
  `pre_order` o
SET
  `goods_thumb_pic` = (
    SELECT
      goods_thumb_pic
    FROM
      pre_goods g
    WHERE
      o.goods_id = g.goods_id
  )
where
  o.goods_thumb_pic = ''
	
	
	
UPDATE
  `pre_order` o
SET
  `goods_name` = (
    SELECT
      goods_name
    FROM
      pre_goods g
    WHERE
      o.goods_id = g.goods_id
  )
where
  o.goods_name = ''



UPDATE
  pre_goods g
SET
    blockchain_address = 'cfx:acbgr4k99p4rhwfnc1ufazzb8htsagjc7uwh7c2hc6'
  WHERE  g.goods_id >=13


  //商品9-12

UPDATE
  pre_goods g
SET
  blockchain_address = (
      SELECT
          blockchain_address
      FROM
          pre_goods g2
      WHERE
          g2.goods_id = 9
  ),
  contract_network = (
      SELECT
          contract_network
      FROM
          pre_goods g2
      WHERE
          g2.goods_id = 9
  ),
  contract_tokenuri_url_domain = (
      SELECT
          contract_tokenuri_url_domain
      FROM
          pre_goods g2
      WHERE
          g2.goods_id = 9
  ),
  contract_tokenuri_url_pre = (
      SELECT
          contract_tokenuri_url_pre
      FROM
          pre_goods g2
      WHERE
          g2.goods_id = 9
  ),
  contract_keystore_path = (
      SELECT
          contract_keystore_path
      FROM
          pre_goods g2
      WHERE
          g2.goods_id = 9
  )
WHERE  g.goods_id >=50


INSERT
    pre_contract_template
SET
    SELECT   blockchain_id,  blockchain_name,  blockchain_key,  blockchain_icon,  blockchain_address,  contract_type,  contract_network,  contract_tokenuri_url_domain,  contract_tokenuri_url_pre,  contract_keystore_path from pre_goods GROUP BY blockchain_address








UPDATE
  pre_goods g
SET
  contract_template_id = (
      SELECT
          contract_template_id
      FROM
          pre_contract_template g2
      WHERE
          g2.blockchain_address = g.blockchain_address
  )





INSERT
    pre_contract_template
SELECT
	NULL AS contract_template_id,
  contract_tokenuri_url_pre AS tile,
	blockchain_id ,
	blockchain_name,
	blockchain_key,
	blockchain_icon,
	blockchain_address,
	contract_type,
	contract_network,
	contract_tokenuri_url_domain,
	contract_tokenuri_url_pre,
	contract_keystore_path,
	'0000-00-00 00:00:00',
	'0000-00-00 00:00:00',
	'0000-00-00 00:00:00'
FROM
	pre_goods
GROUP BY
	blockchain_address




