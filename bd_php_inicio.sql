CREATE DATABASE proj_rastreamento_carnes;
USE proj_rastreamento_carnes;

-- ==================================================================
-- TABELAS DE USUÁRIOS E CARGOS
-- ==================================================================
CREATE TABLE cargos (
  id_cargo INT AUTO_INCREMENT PRIMARY KEY,
  nome_cargo VARCHAR(50) NOT NULL
);

CREATE TABLE usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nome_usuario VARCHAR(120) NOT NULL,
  email_usuario VARCHAR(120) NOT NULL UNIQUE,
  senha_usuario VARCHAR(255) NOT NULL
);

CREATE TABLE usuario_tem_cargos (
  id_usuario INT NOT NULL,
  id_cargo INT NOT NULL,
  PRIMARY KEY (id_usuario, id_cargo),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
  FOREIGN KEY (id_cargo) REFERENCES cargos(id_cargo)
);

-- ==================================================================
-- FORNECEDORES
-- ==================================================================
CREATE TABLE fornecedores (
  id_fornecedor INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  cnpj VARCHAR(20) NOT NULL UNIQUE
);

-- ==================================================================
-- CATEGORIAS E PRODUTOS
-- ==================================================================
CREATE TABLE categoria_produto (
  id_categoria INT AUTO_INCREMENT PRIMARY KEY,
  nome_categoria VARCHAR(60) NOT NULL
);

CREATE TABLE produtos (
  id_produto INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  id_categoria INT,
  FOREIGN KEY (id_categoria) REFERENCES categoria_produto(id_categoria)
);

-- ==================================================================
-- LOTES
-- ==================================================================
CREATE TABLE lotes (
  id_lote INT AUTO_INCREMENT PRIMARY KEY,
  codigo_lote VARCHAR(60) NOT NULL UNIQUE,
  produto_id INT NOT NULL,
  fornecedor_id INT NOT NULL,
  origem VARCHAR(150) NOT NULL,
  destino VARCHAR(150) NOT NULL,
  data_fabricacao DATE,
  data_validade DATE,
  peso DECIMAL(10,2),
  circunstancia ENUM('preparado','em_transito','entregue','armazenado') NOT NULL DEFAULT 'preparado',
  FOREIGN KEY (produto_id) REFERENCES produtos(id_produto),
  FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id_fornecedor)
);

CREATE TABLE produtos_nos_lotes (
  id_produto INT NOT NULL,
  id_lote INT NOT NULL,
  quantidade INT NOT NULL DEFAULT 1,
  PRIMARY KEY (id_produto, id_lote),
  FOREIGN KEY (id_produto) REFERENCES produtos(id_produto),
  FOREIGN KEY (id_lote) REFERENCES lotes(id_lote)
);

-- ==================================================================
-- STATUS DO LOTE
-- ==================================================================
CREATE TABLE status_lote (
  id_status INT AUTO_INCREMENT PRIMARY KEY,
  nome_status VARCHAR(40) NOT NULL
);

CREATE TABLE lote_status_historico (
  id_lote_status INT AUTO_INCREMENT PRIMARY KEY,
  id_lote INT NOT NULL,
  id_status INT NOT NULL,
  id_usuario INT NOT NULL,
  data_evento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_lote) REFERENCES lotes(id_lote),
  FOREIGN KEY (id_status) REFERENCES status_lote(id_status),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- ==================================================================
-- SENSORES E TEMPERATURAS
-- ==================================================================
CREATE TABLE tipo_sensores (
  id_tipo INT AUTO_INCREMENT PRIMARY KEY,
  nome_tipo VARCHAR(50) NOT NULL
);

CREATE TABLE transportadora (
  id_transportadora INT AUTO_INCREMENT PRIMARY KEY,
  nome_transportadora VARCHAR(120) NOT NULL,
  cnpj VARCHAR(20),
  telefone_transportadora VARCHAR(20)
);

CREATE TABLE veiculos (
  id_veiculo INT AUTO_INCREMENT PRIMARY KEY,
  placa_veiculo VARCHAR(10) NOT NULL,
  modelo_veiculo VARCHAR(80),
  id_transportadora INT,
  FOREIGN KEY (id_transportadora) REFERENCES transportadora(id_transportadora)
);

CREATE TABLE sensores (
  id_sensor INT AUTO_INCREMENT PRIMARY KEY,
  id_tipo INT NOT NULL,
  id_veiculo INT,
  descricao VARCHAR(150),
  FOREIGN KEY (id_tipo) REFERENCES tipo_sensores(id_tipo),
  FOREIGN KEY (id_veiculo) REFERENCES veiculos(id_veiculo)
);

CREATE TABLE temperaturas (
  id_temperatura INT AUTO_INCREMENT PRIMARY KEY,
  id_sensor INT NOT NULL,
  id_lote INT,
  temp_reg DECIMAL(5,2) NOT NULL,
  data_reg DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_sensor) REFERENCES sensores(id_sensor),
  FOREIGN KEY (id_lote) REFERENCES lotes(id_lote)
);

-- ==================================================================
-- EVENTOS E TIPOS DE EVENTO
-- ==================================================================
CREATE TABLE tipo_eventos (
  id_tipo INT AUTO_INCREMENT PRIMARY KEY,
  nome_tipo VARCHAR(50) NOT NULL
);

CREATE TABLE eventos (
  id_evento INT AUTO_INCREMENT PRIMARY KEY,
  lote_id INT NOT NULL,
  tipo_evento_id INT,
  descricao TEXT,
  data_evento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (lote_id) REFERENCES lotes(id_lote),
  FOREIGN KEY (tipo_evento_id) REFERENCES tipo_eventos(id_tipo)
);

-- ==================================================================
-- ENDEREÇOS 
-- ==================================================================
CREATE TABLE estado (
  id_estado INT AUTO_INCREMENT PRIMARY KEY,
  sigla_estado CHAR(2) NOT NULL
);

CREATE TABLE cidade (
  id_cidade INT AUTO_INCREMENT PRIMARY KEY,
  nome_cidade VARCHAR(80) NOT NULL,
  id_estado INT NOT NULL,
  FOREIGN KEY (id_estado) REFERENCES estado(id_estado)
);

CREATE TABLE bairro (
  id_bairro INT AUTO_INCREMENT PRIMARY KEY,
  nome_bairro VARCHAR(80) NOT NULL,
  id_cidade INT NOT NULL,
  FOREIGN KEY (id_cidade) REFERENCES cidade(id_cidade)
);

CREATE TABLE logradouro (
  id_logradouro INT AUTO_INCREMENT PRIMARY KEY,
  tipo_logradouro VARCHAR(20),
  nome_logradouro VARCHAR(120),
  id_bairro INT NOT NULL,
  FOREIGN KEY (id_bairro) REFERENCES bairro(id_bairro)
);

CREATE TABLE cep (
  id_cep INT AUTO_INCREMENT PRIMARY KEY,
  cep VARCHAR(9) NOT NULL,
  id_logradouro INT NOT NULL,
  FOREIGN KEY (id_logradouro) REFERENCES logradouro(id_logradouro)
);

CREATE TABLE enderecos (
  id_endereco INT AUTO_INCREMENT PRIMARY KEY,
  id_cep INT NOT NULL,
  complemento_end VARCHAR(150),
  FOREIGN KEY (id_cep) REFERENCES cep(id_cep)
);

-- ==================================================================
-- COMPRADORES
-- ==================================================================
CREATE TABLE compradores (
  id_comprador INT AUTO_INCREMENT PRIMARY KEY,
  nome_comprador VARCHAR(120) NOT NULL,
  cpf_comprador VARCHAR(20),
  id_endereco INT,
  FOREIGN KEY (id_endereco) REFERENCES enderecos(id_endereco)
);

CREATE TABLE comprador_tem_lotes (
  id_comprador INT NOT NULL,
  id_lote INT NOT NULL,
  PRIMARY KEY (id_comprador, id_lote),
  FOREIGN KEY (id_comprador) REFERENCES compradores(id_comprador),
  FOREIGN KEY (id_lote) REFERENCES lotes(id_lote)
);

-- ==================================================================
-- TABELAS DE RELAÇÃO
-- ==================================================================
CREATE TABLE fornecedores_tem_lotes (
  id_fornecedor INT NOT NULL,
  id_lote INT NOT NULL,
  PRIMARY KEY (id_fornecedor, id_lote),
  FOREIGN KEY (id_fornecedor) REFERENCES fornecedores(id_fornecedor),
  FOREIGN KEY (id_lote) REFERENCES lotes(id_lote)
);

CREATE TABLE lotes_veiculos (
  id_lote INT NOT NULL,
  id_veiculo INT NOT NULL,
  data DATE,
  PRIMARY KEY (id_lote, id_veiculo),
  FOREIGN KEY (id_lote) REFERENCES lotes(id_lote),
  FOREIGN KEY (id_veiculo) REFERENCES veiculos(id_veiculo)
);

-- ==================================================================
-- INSERÇÕES DE DADOS
-- ==================================================================

-- CARGOS
INSERT INTO cargos (nome_cargo) VALUES ('admin'), ('funcionario'), ('inspetor');

-- USUÁRIOS
INSERT INTO usuarios (nome_usuario, email_usuario, senha_usuario) VALUES
('Administrador', 'admin@sistema.com', '123456'),
('Funcionário', 'func@sistema.com', '123456');

-- VINCULAÇÕES USUÁRIO x CARGO
INSERT INTO usuario_tem_cargos (id_usuario, id_cargo) VALUES
(1, 1),
(2, 2);

-- FORNECEDORES
INSERT INTO fornecedores (nome, cnpj) VALUES
('Frigorífico Boi Bom', '11.111.111/0001-11'),
('Frigorífico Carne Forte', '22.222.222/0001-22');

-- CATEGORIAS E PRODUTOS
INSERT INTO categoria_produto (nome_categoria) VALUES 
('Bovino'), ('Suíno');

INSERT INTO produtos (nome, id_categoria) VALUES
('Picanha', 1),
('Alcatra', 1),
('Contrafilé', 1),
('Fraldinha', 1),
('Maminha', 1),
('Cupim', 1),
('Lombo Suíno', 2),
('Costela Suína', 2),
('Pernil Suíno', 2),
('Filé Suíno', 2);

-- LOTES
INSERT INTO lotes (codigo_lote, produto_id, fornecedor_id, origem, destino, data_fabricacao, data_validade, peso, circunstancia)
VALUES
('L001', 1, 1, 'Frigorífico Boi Bom', 'Açougue Central', '2025-02-01', '2025-03-01', 150.00, 'preparado'),
('L002', 2, 2, 'Frigorífico Carne Forte', 'Açougue Centro', '2025-02-05', '2025-03-05', 180.00, 'em_transito');

-- PRODUTOS NOS LOTES
INSERT INTO produtos_nos_lotes (id_produto, id_lote, quantidade) VALUES
(1, 1, 150),
(2, 2, 180);

-- STATUS DO LOTE
INSERT INTO status_lote (nome_status) VALUES
('preparado'), ('em_transito'), ('entregue'), ('armazenado');

-- HISTÓRICO DE STATUS
INSERT INTO lote_status_historico (id_lote, id_status, id_usuario, data_evento) VALUES
(1, 1, 1, '2025-02-01 09:00:00'),
(1, 2, 2, '2025-02-02 10:30:00');

-- TIPOS DE SENSORES
INSERT INTO tipo_sensores (nome_tipo) VALUES ('temperatura'), ('umidade');

-- TRANSPORTADORA E VEÍCULOS
INSERT INTO transportadora (nome_transportadora, cnpj, telefone_transportadora) VALUES
('TransLog', '00.000.000/0001-00', '11999999999');

INSERT INTO veiculos (placa_veiculo, modelo_veiculo, id_transportadora) VALUES
('XCI8E65', 'Volvo FH', 1),
('RGA0J12', 'Mercedes Atego', 1);

-- SENSORES
INSERT INTO sensores (id_tipo, id_veiculo, descricao) VALUES
(1, 1, 'Sensor traseiro - caminhão 1'),
(1, 2, 'Sensor compartimento - caminhão 2');

-- TEMPERATURAS (ligadas a sensor e opcionalmente a um lote)
INSERT INTO temperaturas (id_sensor, id_lote, temp_reg, data_reg) VALUES
(1, 1, 3.8, '2025-02-01 09:15:00'),
(1, 1, 4.2, '2025-02-01 10:00:00'),
(2, 2, 3.5, '2025-02-05 11:20:00');

-- TIPOS DE EVENTOS E EVENTOS
INSERT INTO tipo_eventos (nome_tipo) VALUES ('Carregado'), ('Inspecionado'), ('Descarregado');
INSERT INTO eventos (lote_id, tipo_evento_id, descricao, data_evento) VALUES
(1, 1, 'Lote carregado no caminhão', '2025-02-01 09:00:00'),
(1, 2, 'Inspeção realizada. Temperatura ok.', '2025-02-01 10:05:00'),
(2, 1, 'Lote carregado na fábrica', '2025-02-05 08:45:00');

-- ENDEREÇOS (exemplo)
INSERT INTO estado (sigla_estado) VALUES ('SP'), ('MG');
INSERT INTO cidade (nome_cidade, id_estado) VALUES ('São Paulo', 1), ('Belo Horizonte', 2);
INSERT INTO bairro (nome_bairro, id_cidade) VALUES ('Centro', 1), ('Savassi', 2);
INSERT INTO logradouro (tipo_logradouro, nome_logradouro, id_bairro) VALUES ('Av.', 'Paulista', 1), ('R.', 'Pampulha', 2);
INSERT INTO cep (cep, id_logradouro) VALUES ('01311000', 1), ('30140071', 2);
INSERT INTO enderecos (id_cep, complemento_end) VALUES (1, 'Próximo ao metrô'), (2, 'Loja 5');

-- COMPRADORES
INSERT INTO compradores (nome_comprador, cpf_comprador, id_endereco) VALUES
('Mercado Central', '111.222.333-44', 1),
('Supermercado Bom Preço', '555.666.777-88', 2);

-- COMPRA / VENDA (liga comprador x lote)
INSERT INTO comprador_tem_lotes (id_comprador, id_lote) VALUES
(1, 1),
(2, 2);

-- FORNECEDORES TEM LOTES (liga fornecedor x lote)
INSERT INTO fornecedores_tem_lotes (id_fornecedor, id_lote) VALUES
(1, 1),
(2, 2);

-- LOTE x VEÍCULO (rota)
INSERT INTO lotes_veiculos (id_lote, id_veiculo, data) VALUES
(1, 1, '2025-02-01'),
(2, 2, '2025-02-05');