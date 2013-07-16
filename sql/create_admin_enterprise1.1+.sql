# @author David G Vigil <davidv@zerolag.com>
# This is for Enterprise version 1.10+ 
# password is: MagnetoCheat

INSERT INTO admin_user
SELECT
	(SELECT max(user_id) + 1 FROM admin_user) user_id,
	'Bob' first_name,
	'Saget' last_name,
	'bob@bobsagetisgod.com' email,
	'bobsaget' username,
	'7cc7c895dee1d6b4c068d8ed986f5908:mk' password,
	now() created,
	NULL modified,
	NULL logdate,
	0 lognum,
	0 reload_acl_flag,
	1 is_active,
	(SELECT max(extra) FROM admin_user WHERE extra is not null) extra,
	NULL rp_token,
	NOW() rp_token_created_at
	NULL first_failure,
	NULL lock_expires;
 
INSERT INTO admin_role
SELECT
	(SELECT max(role_id) + 1 FROM admin_role) role_id,
	(SELECT role_id FROM admin_role WHERE role_name = 'Administrators') parent_id,
	2 tree_level,
	0 sort_order,
	'U' role_type,
	(SELECT user_id FROM admin_user WHERE username = 'bobsaget') user_id,
	'bobsaget' role_name
	1 gws_is_all,
	NULL gws_websites,
	NULL gws_store_groups;
