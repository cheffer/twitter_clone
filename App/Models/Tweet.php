<?php

namespace App\Models;

use MF\Model\Model;

class Tweet extends Model {

	private $id;
	private $id_usuario;
	private $tweet;
	private $data;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

	//salvar
	public function salvar() {
		$query = "insert into tweets(id_usuario, tweet) values(:id_usuario, :tweet)";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->bindValue(':tweet', $this->__get('tweet'));
		$stmt->execute();

		return $this;
	}

	public function remover() {
		$query = "delete from tweets where id = :id_tweet and id_usuario = :id_usuario";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_tweet', $_GET['id_tweet']);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();

		return true;
	}

	//recuperar
	public function getAll() {
		$query = "
			select 
				t.id, 
				t.id_usuario, 
				u.nome,
				t.tweet, 
				DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
			from 
				tweets t
				left join usuarios u on t.id_usuario = u.id
			where 
				t.id_usuario = :id_usuario
				or t.id_usuario in (select id_usuario_seguindo from usuarios_seguidores where id_usuario = :id_usuario)
			order by t.data desc
		";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	//recuperar por pagina
	public function getRegistroPorPagina($limit, $offset) {
		$query = "
			select 
				t.id, 
				t.id_usuario, 
				u.nome,
				t.tweet, 
				DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
			from 
				tweets t
				left join usuarios u on t.id_usuario = u.id
			where 
				t.id_usuario = :id_usuario
				or t.id_usuario in (select id_usuario_seguindo from usuarios_seguidores where id_usuario = :id_usuario)
			order by t.data desc
			limit 
				$limit
			offset
				$offset
		";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getTotalRegistros() {
		$query = "
			select 
				count(*) as total_registros
			from 
				tweets t
				left join usuarios u on t.id_usuario = u.id
			where 
				t.id_usuario = :id_usuario
				or t.id_usuario in (select id_usuario_seguindo from usuarios_seguidores where id_usuario = :id_usuario)
		";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

}


?>