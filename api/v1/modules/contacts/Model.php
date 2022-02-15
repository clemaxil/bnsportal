<?php

class ContactsModel extends AppModel
{
	
    public function __construct()
	{
        parent::__construct();
		parent::getConnection();
    }




    public function getContactByApiKey($apiKey)
	{		
        $sth = $this->db->prepare("SELECT id FROM contacts WHERE api_key LIKE :api_key AND deleted = 0 LIMIT 1");
        $sth->bindParam(':api_key', $apiKey, PDO::PARAM_STR);
        $sth->execute();
        return $sth->fetch(); 
    }




    public function read($fields=array('*'), $where='1=1', $sortby='id', $order = 'ASC', $limit=50, $offset=0){ 
        try{
            $query = 'SELECT '.$fields.' FROM contacts WHERE '.$where.' ORDER BY '.$sortby.' '.$order.' LIMIT '.$limit.' OFFSET '.$offset;
            $sth = $this->db->prepare($query);
            $sth->execute();
        }
        catch (PDOException $Exception) {
            return(array('error'=>$Exception->getMessage( ).' '.$Exception->getCode( )));
        }
        return $sth->fetchAll();
    }




    public function countAll($queryWhere='1=1'){         
        try{
            $query = 'SELECT count(id) as total FROM contacts WHERE '.$queryWhere;
            $sth = $this->db->prepare($query);
            $sth->execute();
        }
        catch (PDOException $Exception) {
            return(array('error'=>$Exception->getMessage( ).' '.$Exception->getCode( )));
        }        
        return $sth->fetch();
    }




    public function create(object $params)
	{		        
        $sth = $this->db->prepare("INSERT INTO `contacts` (
            `id_ext`, `created_at`, `updated_at`, `first_name`, `last_name`, `email`, 
            `phone_mobile`, `address_street`, `address_postalcode`, `address_city`, `address_country`, `birthdate`, `lang`, `calendars`) 
            VALUES
            (:id_ext, now(), now(), :first_name, :last_name, :email,
            :phone_mobile, :address_street, :address_postalcode, :address_city, :address_country, :birthdate, 'fr', :calendars)");

        $sth->bindParam(':id_ext', $params->id_ext);
        $sth->bindParam(':first_name', $params->first_name, PDO::PARAM_STR);
        $sth->bindParam(':last_name', $params->last_name, PDO::PARAM_STR);
        $sth->bindParam(':email', $params->email, PDO::PARAM_STR);
        $sth->bindParam(':phone_mobile', $params->phone_mobile);
        $sth->bindParam(':address_street', $params->address_street, PDO::PARAM_STR);
        $sth->bindParam(':address_postalcode', $params->address_postalcode);
        $sth->bindParam(':address_city', $params->address_city, PDO::PARAM_STR);
        $sth->bindParam(':address_country', $params->address_country, PDO::PARAM_STR);
        $sth->bindParam(':birthdate', $params->birthdate);
        $sth->bindParam(':calendars', json_encode($params->calendars));

        $sth->execute();
        return($this->getById( $this->db->lastInsertId() ));       
    }


    public function getByEmail($email)
    {
        $sth = $this->db->prepare("SELECT * FROM contacts WHERE email LIKE :email");
        $sth->bindParam(':email', $email, PDO::PARAM_STR);
        $sth->execute();
        return $sth->fetch();
    }


    public function getById($id)
    {
        $sth = $this->db->prepare("SELECT * FROM contacts WHERE id = :id ");
        $sth->bindParam(':id', $id);
        $sth->execute();
        return $sth->fetch();
    }



    public function delete($id)
    {
        $sth = $this->db->prepare("UPDATE contacts SET deleted='1' WHERE id = :id ");
        $sth->bindParam(':id', $id);
        $sth->execute();
        return( $this->getById($id) );      
    }



    public function update($id, object $params)
    {		
        
        //var_dump( $params ) ;
        
        $queryFields = "updated_at = now(), ";
        if( !empty($params->id_ext) ){ $queryFields .= "id_ext = :id_ext, "; }
        if( !empty($params->first_name) ){ $queryFields .= "first_name = :first_name, "; }
        if( !empty($params->last_name) ){ $queryFields .= "last_name = :last_name, "; }
        if( !empty($params->email) ){ $queryFields .= "email = :email, "; }
        if( !empty($params->phone_mobile) ){ $queryFields .= "phone_mobile = :phone_mobile, "; }
        if( !empty($params->address_street) ){ $queryFields .= "address_street = :address_street, "; }
        if( !empty($params->address_postalcode) ){ $queryFields .= "address_postalcode = :address_postalcode, "; }
        if( !empty($params->address_city) ){ $queryFields .= "address_city = :address_city, "; }
        if( !empty($params->address_country) ){ $queryFields .= "address_country = :address_country, "; }
        if( !empty($params->birthdate) ){ $queryFields .= "birthdate = :birthdate, "; }
        if( !empty($params->calendars) ){ $queryFields .= "calendars = :calendars, "; }
        
        $query = "UPDATE contacts SET ".substr($queryFields,0,-2)." WHERE id = :id ";
        $sth = $this->db->prepare($query);
 
        $sth->bindParam(':id', $id);
        if( !empty($params->id_ext) ){ $sth->bindParam(':id_ext', $params->id_ext); }
        if( !empty($params->first_name) ){ $sth->bindParam(':first_name', $params->first_name, PDO::PARAM_STR); }
        if( !empty($params->last_name) ){ $sth->bindParam(':last_name', $params->last_name, PDO::PARAM_STR); }
        if( !empty($params->email) ){ $sth->bindParam(':email', $params->email, PDO::PARAM_STR); }
        if( !empty($params->phone_mobile) ){ $sth->bindParam(':phone_mobile', $params->phone_mobile); }
        if( !empty($params->address_street) ){ $sth->bindParam(':address_street', $params->address_street, PDO::PARAM_STR); }
        if( !empty($params->address_postalcode) ){ $sth->bindParam(':address_postalcode', $params->address_postalcode); }
        if( !empty($params->address_city) ){ $sth->bindParam(':address_city', $params->address_city, PDO::PARAM_STR); }
        if( !empty($params->address_country) ){ $sth->bindParam(':address_country', $params->address_country, PDO::PARAM_STR); }
        if( !empty($params->birthdate) ){ $sth->bindParam(':birthdate', $params->birthdate); }
        if( !empty($params->calendars) ){ $sth->bindParam(':calendars', json_encode($params->calendars)); }

        $sth->execute();
        return( $this->getById($id) ); 
    }

}