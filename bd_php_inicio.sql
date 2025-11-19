CREATE DATABASE rastreamento_carnes;
USE rastreamento_carnes;

/*=====================================
	 TABELA DE USUÁRIOS E CARGOS
=====================================*/

CREATE TABLE cargos (
id_cargo INT PRIMARY KEY,
nome_cargo VARCHAR(50)
);

CREATE TABLE usuario (
id_usuario INT PRIMARY KEY,
nome_usuario VARCHAR(120),
email_usuario VARCHAR(120),
senha_usuario VARCHAR(255)
);

CREATE TABLE usuario_tem_cargos (
id_usuario INT,
id_cargo INT,
PRIMARY KEY (id_usuario, id_cargo),
FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
FOREIGN KEY (id_cargo) REFERENCES cargos(id_cargo)
);
/*=====================================
	 TABELA DE FORNECEDORES
=====================================*/

CREATE TABLE fornecedores (
id INT AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(150) NOT NULL,
cnpj VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE fornecedores_tem_lotes (
id_fornecedor INT,
id_lote INT,
PRIMARY KEY (id_fornecedor, id_lote),
FOREIGN KEY (id_fornecedor) REFERENCES fornecedores(id_fornecedor),
FOREIGN KEY (id_lote) REFERENCES lotes(id_lote)
);

/*=====================================
 TABELA DE PRODUTOS (TIPOS DE CARNE)
=====================================*/

CREATE TABLE produtos (
id INT AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(120) NOT NULL,
categoria VARCHAR(50) NOT NULL
);

create table categoria_produto (
id_categoria int not null primary key,
nome_categoria varchar (60)
); 

/*=====================================
	 TABELA DE LOTES DE CARNE
=====================================*/

CREATE TABLE lotes (
id INT AUTO_INCREMENT PRIMARY KEY,
codigo_lote VARCHAR(60) NOT NULL UNIQUE,
produto_id INT NOT NULL,
fornecedor_id INT NOT NULL,
origem VARCHAR(150) NOT NULL,
destino VARCHAR(150) NOT NULL,
data_fabricacao DATE,
data_validade DATE,
peso DECIMAL(10,2),
circunstancia ENUM('preparado','em trânsito','entregue','armazenado') NOT NULL DEFAULT 'preparado',
CONSTRAINT FOREIGN KEY (produto_id) REFERENCES produtos(id),
CONSTRAINT FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id)
);

CREATE TABLE produtos_nos_lotes (
id_produto INT,
id_lote INT,
quantidade INT,
PRIMARY KEY (id_produto, id_lote),
FOREIGN KEY (id_produto) REFERENCES produtos(id_produto),
FOREIGN KEY (id_lote) REFERENCES lotes(id_lote)
);

CREATE TABLE status_lote (
id_status INT PRIMARY KEY,
nome_status VARCHAR(40)
);

CREATE TABLE lote_status_historico (
id_lote_status INT PRIMARY KEY,
id_lote INT NOT NULL,
id_status INT NOT NULL,
id_usuario INT NOT NULL,
data_evento DATETIME,
FOREIGN KEY (id_lote) REFERENCES lotes(id_lote),
FOREIGN KEY (id_status) REFERENCES status_lote(id_status),
FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);


/*=====================================
 TABELA DE TEMPERATURAS DO LOTE
=====================================*/

CREATE TABLE tipo_sensores (
id_tipo INT PRIMARY KEY,
nome_tipo VARCHAR(20)
);

CREATE TABLE sensores (
id_sensor INT PRIMARY KEY,
id_tipo INT NOT NULL,
id_veiculo INT NOT NULL,
FOREIGN KEY (id_tipo) REFERENCES tipo_sensores(id_tipo),
FOREIGN KEY (id_veiculo) REFERENCES veiculos(id_veiculo)
);

CREATE TABLE temperaturas (
id_temperatura INT PRIMARY KEY,
id_sensor INT NOT NULL,
temp_reg DECIMAL(5,2),
data_reg DATETIME,
FOREIGN KEY (id_sensor) REFERENCES sensores(id_sensor)
);


/*=====================================
 TABELA DE EVENTOS DO LOTE
=====================================*/

CREATE TABLE eventos (
id INT AUTO_INCREMENT PRIMARY KEY,
lote_id INT NOT NULL,
tipo VARCHAR(50) NOT NULL,
descricao TEXT,
data_evento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (lote_id) REFERENCES lotes(id)
);

create table tipo_eventos (
id_tipo int not null,
nome_tipo varchar (50)
);

/*======================================
TABELAS DE ENDEREÇO 
=======================================*/

create table estado (
id_estado int not null primary key,
sigla_estado char (2)
);


create table cidade (
id_cidade int not null primary key,
nome_cidade varchar (80),
id_estado int not null,
foreign key (id_estado) references estado (id_estado)
);


create table bairro(
id_bairro int not null primary key, 
nome_bairro varchar (80),
id_cidade int not null,
foreign key (id_cidade) references cidade (id_cidade)
);


create table logradouro (
id_logradouro int not null primary key,
tipo_logradouro varchar (20),
nome_logradouro varchar (120),
id_bairro int not null,
foreign key (id_bairro) references bairro (id_bairro)
);

create table cep (
id_cep int not null primary key,
cep varchar (8),
id_logradouro int not null, 
foreign key (id_logradouro) references logradouro (id_logradouro)
);

CREATE TABLE enderecos (
id_endereco INT PRIMARY KEY,
id_cep INT NOT NULL,
complemento_end VARCHAR(150),
FOREIGN KEY (id_cep) REFERENCES cep(id_cep)
);

/*=====================================
TABELAS DOS VEÍCULOS DE TRANSPORTE
======================================*/

create table transportadora (
id_transportadora int not null,
nome_branco varchar (120),
cnpj varchar (20),
telefone_transportadora varchar (20)
);

create table veiculos (
id_veiculo int not null,
placa_veiculo varchar (10),
modelo_veiculo varchar (80)
);

CREATE TABLE lotes_veiculos (
id_lote INT,
id_veiculo INT,
data DATE,
PRIMARY KEY (id_lote, id_veiculo),
FOREIGN KEY (id_lote) REFERENCES lotes(id_lote),
FOREIGN KEY (id_veiculo) REFERENCES veiculos(id_veiculo)
);


/*=====================================
TABELA COMPRADORES
=====================================*/
CREATE TABLE compradores (
id_comprador INT PRIMARY KEY,
nome_comprador VARCHAR(120),
cpf_comprador VARCHAR(20),
id_endereco INT NOT NULL,
FOREIGN KEY (id_endereco) REFERENCES enderecos(id_endereco)
);

CREATE TABLE comprador_tem_lotes (
id_comprador INT,
id_lote INT,
PRIMARY KEY (id_comprador, id_lote),
FOREIGN KEY (id_comprador) REFERENCES compradores(id_comprador),
FOREIGN KEY (id_lote) REFERENCES lotes(id_lote)
);


/*=====================================
	 USUÁRIOS
=====================================*/
INSERT INTO usuarios (nome, email, senha, cargo)
VALUES
('Administrador', 'admin@sistema.com', '123456', 'admin'),
('Funcionário', 'func@sistema.com', '123456', 'funcionario');

/*=====================================
	 FORNECEDORES
=====================================*/
INSERT INTO fornecedores (nome, cnpj)
VALUES
('Frigorífico Boi Bom', '11.111.111/0001-11'),
('Frigorífico Carne Forte', '22.222.222/0001-22');

/*=====================================
	 PRODUTOS
======================================*/
INSERT INTO produtos (nome, categoria)
VALUES
('Picanha Bovina', 'Bovino'),
('Alcatra Bovina', 'Bovino'),
('Lombo Suíno', 'Suíno');

/*=====================================
	LOTES
======================================*/
INSERT INTO lotes (codigo_lote, produto_id, fornecedor_id, origem, destino, data_fabricacao, data_validade, peso, circunstancia)
VALUES
('L001', 1, 1, 'Frigorífico Boi Bom', 'Açougue Central', '2025-02-01', '2025-03-01', 150.00, 'preparado'),
('L002', 2, 2, 'Frigorífico Carne Forte', 'Açougue Centro', '2025-02-05', '2025-03-05', 180.00, 'transito');

/*=====================================
	 TEMPERATURAS
=====================================*/
INSERT INTO temperaturas (lote_id, temperatura)
VALUES
(1, 3.8),
(1, 4.2),
(2, 3.5);

/*=====================================
	 EVENTOS
=====================================*/
INSERT INTO eventos (lote_id, tipo, descricao)
VALUES
(1, 'Carregado', 'Lote carregado no caminhão'),
(1, 'Inspecionado', 'Inspeção realizada. Temperatura ok.'),
(2, 'Carregado', 'Transportadora iniciou deslocamento');
