<?php

class User {

	protected $bdd;
	protected $id_user;
	// About user/sender
	protected $id_user_theme;
	protected $firstname_user;
	protected $pseudo_user;
	protected $status_user;
	protected $css_status;
	protected $email_user;
	protected $friends_list;
	protected $conversation;
	// About receiver
	protected $id_receiver;
	protected $firstname_receiver;
	protected $pseudo_receiver;
	protected $id_receiver_status;
	protected $css_receiver_status;

	public function __construct($bdd, $id_user) {
		$this->bdd = $bdd;
		$this->id_user = $id_user;
	}

	public function getUserProfile() {

		$request = "SELECT
			DISTINCT id_user,
			email,
			firstname,
			pseudo,
			user_status,
			fk_color_scheme,
			css_status
			FROM user
			LEFT OUTER JOIN user_status ON fk_user_status = id_user_status
			WHERE id_user = :id_user";

		$stmt = $this->bdd->prepare($request,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->execute(array(
  			':id_user' => $this->id_user
  		));

    	while ($profile = $stmt->fetch(PDO::FETCH_OBJ)) {
        	$this->firstname_user = $profile->firstname;
			$this->pseudo_user = $profile->pseudo;
			$this->status_user = $profile->user_status;
			$this->css_status = $profile->css_status;
			$this->email_user = $profile->email;
			$this->id_user_theme = $profile->fk_color_scheme;
    	}

	}

	public function getUserFriends($limit = 0) {

		$request = "SELECT
			user.id_user,
			user.pseudo AS user_pseudo,
			friend.id_user AS id_friend,
			friend.pseudo AS friend_pseudo,
			friend.firstname AS friend_firstname,
			css_status,
			user_status AS friend_status
			FROM user_friend
			LEFT OUTER JOIN user ON fk_user = user.id_user
			LEFT OUTER JOIN user AS friend ON fk_friend = friend.id_user
			LEFT OUTER JOIN user_status ON user_status.id_user_status = friend.fk_user_status
			WHERE user.id_user = $this->id_user";

		if ($limit != 0) {
			$request .= " LIMIT $limit";
		}

		$friends = $this->bdd->query($request);
		return $this->friends_list = $friends;

	}

	public function getConversationWith($id_recepteur, $limit = 0, $order = 'ASC') {

		$request = 'SELECT
				emetteur.id_user AS id_user,
				recepteur.id_user AS id_recepteur,
				user_message,
				DATE_FORMAT(msg_date, "%H:%i") AS hour,
				DATE_FORMAT(msg_date, "%d/%m") AS day,
				DATE_FORMAT(msg_date, "%Y") AS year,
				emetteur.pseudo AS pseudo_emetteur,
				emetteur.firstname AS firstname_emetteur,
				recepteur.pseudo AS pseudo_recepteur,
				recepteur.firstname AS firstname_recepteur
				FROM message
				LEFT OUTER JOIN user AS emetteur ON fk_user = emetteur.id_user
				LEFT OUTER JOIN user AS recepteur ON fk_user_recepteur = recepteur.id_user
				WHERE fk_user = '.$this->id_user.' AND fk_user_recepteur = '.$id_recepteur.'
				OR fk_user = '.$id_recepteur.' AND fk_user_recepteur = '.$this->id_user.'
				ORDER BY msg_date '.$order;

		if ( $limit != 0 ) {
			$request .= " LIMIT $limit";
		}

		$conversation = $this->bdd->query($request);
		return $this->conversation = $conversation;
		
	}

	public function getReceiverProfile($id_recepteur) {

		$receiver = $this->bdd->query('
			SELECT
			DISTINCT recepteur.id_user AS id_recepteur,
			recepteur.pseudo AS pseudo_recepteur,
			recepteur.firstname AS firstname_recepteur,
			css_recepteur_statut.css_status AS css_recepteur,
			recepteur.fk_user_status AS id_recepteur_status
			FROM message
			LEFT OUTER JOIN user AS emetteur ON fk_user = emetteur.id_user
			LEFT OUTER JOIN user AS recepteur ON fk_user_recepteur = recepteur.id_user
			LEFT OUTER JOIN user_status AS css_recepteur_statut ON css_recepteur_statut.id_user_status = recepteur.fk_user_status
			WHERE fk_user = '.$this->id_user.' AND fk_user_recepteur ='.$id_recepteur
		);

		foreach ($receiver->fetchAll(PDO::FETCH_OBJ) as $receiver) {
			$this->id_receiver = $receiver->id_recepteur;
			$this->firstname_receiver = $receiver->firstname_recepteur;
			$this->pseudo_receiver = $receiver->pseudo_recepteur;
			$this->css_receiver_status = $receiver->css_recepteur;
			$this->id_receiver_status = $receiver->id_recepteur_status;
		}

	}

	// About user/receiver
	public function getReceiverID() {
		return $this->id_receiver;
	}

	public function getReceiverCSS() {
		return $this->css_receiver_status;
	}

	public function getReceiverIDStatus() {
		return $this->id_receiver_status;
	}

	public function getReceiverFirstname() {
		return $this->firstname_receiver;
	}

	public function getReceiverPseudo() {
		return $this->pseudo_receiver;
	}

	public function set_userStatus($id_status) {
		return $this->id_user_status = $id_status;
	}

	// About user/sender
	public function getFirstname() {
		return $this->firstname_user;
	}

	public function getPseudo() {
		return $this->pseudo_user;
	}

	public function getEmail() {
		return $this->email_user;
	}

	public function getStatus() {
		return $this->user_status;
	}

	public function getCssStatus() {
		return $this->css_status;
	}

	public function getFriends() {
		return $this->friends_list;
	}

	/*public function getUserConversation() {
		return $this->conversation;
	}*/

	public function getDesignID() {
		return $this->id_user_theme;
	}

}

?>
