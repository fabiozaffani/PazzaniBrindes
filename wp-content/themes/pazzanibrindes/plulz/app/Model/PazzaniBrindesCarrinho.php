<?php

if (!class_exists('PazzaniBrindesCarrinho'))
{
    class PazzaniBrindesCarrinho extends PlulzObjectAbstract
    {

        /**
         * The total ammount of itens in the current cart
         * @var int
         */
        protected $_totalItens;

        /**
         * Holds the PlulzSession object
         * @var Object
         */
        protected $_PlulzSession;

        /**
         * Holds all cart information
         * @var array
         */
        protected $_Carrinho;

        /**
         * Holds produtos information
         * @var Object
         */
        public $PlulzProduto;

        public function __construct()
        {
            $this->_name            =   'pazzanibrindes_carrinho';

            $this->PlulzProduto     =   new PazzaniBrindesProduto();

            // Init the _session
            $this->_PlulzSession    =   PlulzSession::getInstance();

            $this->_Carrinho        =   $this->_PlulzSession->isDefined('carrinho') ? $this->_PlulzSession->get('carrinho') : array();

            $this->_totalItens      =   count($this->_Carrinho);

            parent::__construct();

        }

        /**
         * Adds new produtos to the cart
         * @param $prod_id
         * @param $quantidade
         * @return null|string
         */
		public function AddtoCart( $prod_id, $quantidade )
		{
            if ( empty($prod_id) || empty($quantidade))
                return false;

            $this->PlulzProduto->id = $prod_id;

            // Checar se a quantidade é um número válido
            preg_match('/^[1-9]+[0-9]*$/', $quantidade, $matches); //$total so pode aceitar numeros

            if ( !$matches )
                return false;

            $produto['minimo']  =   $this->PlulzProduto->getMinimo();

            if ( $quantidade < $produto['minimo'] )
                return false;

            if( $this->isProductInCart( $prod_id ) )
            {
                $this->UpdateCart( $prod_id, $quantidade);
            }
            else
            {
                $this->_Carrinho[$prod_id]  =  $quantidade;

                $this->_PlulzSession->set('carrinho', $this->_Carrinho);
            }

            return true;
		}

        /**
         * Remove itens from cart but only after checking if the product exists
         * @param $prod_id
         * @return bool
         */
		public function RemoveFromCart( $prod_id )
		{
            if ( empty($prod_id) )
                return false;

            if ( isset($this->_Carrinho[$prod_id]) )
            {
                unset($this->_Carrinho[$prod_id]);

                $this->_PlulzSession->set('carrinho', $this->_Carrinho);

                return true;
            }
            return false;

		}

        /**
         * Update the cart with the current quantity of the a product
         * @param $prod_id
         * @param $quantidade
         * @return bool
         */
		public function UpdateCart( $prod_id, $quantidade )
		{
            preg_match('/^[1-9]+[0-9]*$/', $quantidade, $matches);

            // There isnt only numbers on quantidade
            if ( !$matches )
                return false;

            if ( !$this->isProductInCart( $prod_id ) )
            {
                return $this->AddtoCart($prod_id, $quantidade);
            }
            else if ( $this->isProductInCart( $prod_id ) && $quantidade == 0 )
            {
                return $this->RemoveFromCart( $prod_id );
            }
            else
            {
                $this->PlulzProduto->id = $prod_id;

                $minimo  =   $this->PlulzProduto->getMinimo();

                // We cant update if the quantidade is menor que o minimo aceito
                if ( $quantidade < $minimo )
                    return false;

                $this->_Carrinho[$prod_id] = $quantidade;

                $this->_PlulzSession->set('carrinho', $this->_Carrinho);
            }

            return true;
		}

        /**
         * Check if the product is already added to the cart through the product id
         * @param $prod_id
         * @return boolean
         */
		public function isProductInCart( $prod_id )
		{
            if ( empty($prod_id) )
                return false;

            if( isset($this->_Carrinho[$prod_id]))
                return true;

            return false;
		}

        public function getAllItens()
        {
            return $this->_Carrinho;
        }

        public function itensInCart()
        {
            return $this->_totalItens;
        }

        public function clearCart()
        {
            $this->_PlulzSession->clear('carrinho');
        }

        public function getFechamento()
        {
            $option = get_option($this->_name);

            return $option['fechamento'];
        }

        public function getSucesso()
        {
            $option = get_option($this->_name);

            return $option['sucesso'];
        }
    }
}