<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {


	public function timeline() {

		$this->validaAutenticacao();

		//recuperar tweets
		$tweet = Container::getModel('Tweet');

		$tweet->__set('id_usuario', $_SESSION['id']);		

		//variaveis de paginacao

		$total_registros_pagina = 10;
		$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
		$deslocamento = ($pagina - 1) * $total_registros_pagina;

		//$tweets = $tweet->getAll();
		$tweets = $tweet->getRegistroPorPagina($total_registros_pagina, $deslocamento);
		$total_tweets = $tweet->getTotalRegistros();
		
		$this->view->total_de_paginas = ceil($total_tweets['total_registros'] / $total_registros_pagina);
		$this->view->pagina_ativa = $pagina;


		$this->view->tweets = $tweets;



		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		$this->view->info_usuario = $usuario->getInfoUsuario();
		$this->view->total_tweets = $usuario->getTotalTweets();
		$this->view->total_seguindo = $usuario->getTotalSeguindo();
		$this->view->total_seguidores = $usuario->getTotalSeguidores();

		$this->render('timeline');
		
	}

	public function tweet() {

		$this->validaAutenticacao();
			
		$tweet = Container::getModel('tweet');

		$tweet->__set('tweet', $_POST['tweet']);
		$tweet->__set('id_usuario', $_SESSION['id']);

		$tweet->salvar();

		header('Location: /timeline');
		
	}

	public function removerTweet() {

		$this->validaAutenticacao();

		$tweet = Container::getModel('tweet');

		$tweet->__set('id_usuario', $_SESSION['id']);

		$tweet->remover();

		header('Location: /timeline');

	}

	public function quemSeguir() {

		$this->validaAutenticacao();

		$pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';
		
		$pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : $pesquisa;

		$this->view->teste = $pesquisarPor;

		$usuarios = array();

		$total_usuarios_pagina = 10;
		//$deslocamento = 0;
		$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
		$deslocamento = ($pagina - 1) * $total_usuarios_pagina;

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		$total_usuarios = $usuario->getUsuarioTotal();

		$this->view->info_usuario = $usuario->getInfoUsuario();
		$this->view->total_tweets = $usuario->getTotalTweets();
		$this->view->total_seguindo = $usuario->getTotalSeguindo();
		$this->view->total_seguidores = $usuario->getTotalSeguidores();

		$total_usuario_seguindo = $this->view->total_seguindo['total_seguindo'];

		if($pesquisarPor != '') {			
			
			$usuario->__set('nome', $pesquisarPor);
			
			//$usuarios = $usuario->getAll();
			$usuarios = $usuario->getUsuarioPorPagina($total_usuarios_pagina, $deslocamento);
			$this->view->total_de_paginas = ceil($total_usuarios['total_usuarios'] / $total_usuarios_pagina);	
		} else {
			$usuarios = $usuario->getUsuarioSeguindo($total_usuarios_pagina, $deslocamento);			
			$this->view->total_de_paginas = ceil($total_usuario_seguindo / $total_usuarios_pagina);	
		}
		

		$this->view->pesquisa_por = $pesquisarPor;
		$this->view->pagina_ativa = $pagina;

		
		$this->view->usuarios = $usuarios;

		

		$this->render('quemSeguir');
		
	}

	public function validaAutenticacao() {

		session_start();

		if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			header('Location: /?login=erro');
		} 

	}

	public function acao() {

		$this->validaAutenticacao();

		$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
		$id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		if($acao == 'seguir'){
			$usuario->seguirUsuario($id_usuario_seguindo);
		} else if($acao == 'deixar_de_seguir'){
			$usuario->deixarSeguirUsuario($id_usuario_seguindo);
		}

		header('Location: /quem_seguir');
	}







}

?>