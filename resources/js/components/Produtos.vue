<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">


                <!-- início do card de listagem de Produtos -->
                <card-component titulo="Relação de Produtos">
                    <template v-slot:conteudo>
                        <table-component
                            
                            :dados="marcas.data"
                            :titulos="{
                                id: {titulo: 'ID', tipo: 'texto'},
                                nome: {titulo: 'Nome', tipo: 'texto'},
                                categoria_id: {titulo: 'Categoria', tipo: 'texto'},
                                //imagem: {titulo: 'Imagem', tipo: 'imagem'},
                                created_at: {titulo: 'Criação', tipo: 'data'},
                            }"
                        ></table-component>
                    </template>

                    <template v-slot:rodape>
                        <div class="row">
                            <div class="col-10">
                                <paginate-component>
                                    <li v-for="l, key in marcas.links" :key="key"
                                        :class="l.active ? 'page-item active' : 'page-item'"
                                        @click="paginacao(l)"
                                    >
                                        <a class="page-link" v-html="l.label"></a>
                                    </li>
                                </paginate-component>
                            </div>

                            <div class="col">
                                <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#modalMarca">Adicionar</button>
                            </div>
                        </div>
                    </template>
                </card-component>
                <!-- fim do card de listagem de marcas -->
            </div>
        </div>



        <modal-component id="modalMarca" titulo="Adicionar Produto">

            <template v-slot:alertas>
                <alert-component tipo="success" :detalhes="transacaoDetalhes" titulo="Cadastro realizado com sucesso" v-if="transacaoStatus == 'adicionado'"></alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar a marca" v-if="transacaoStatus == 'erro'"></alert-component>
            </template>

            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Nome do Produto" id="novoNome" id-help="novoNomeHelp">
                        <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp"  v-model="nomeMarca">
                    </input-container-component>
                    
                </div>

                <div class="form-group">
                    <input-container-component titulo="Nome da categoria" id="novoCategoria" id-help="novoCategoriaHelp">
                        <input type="text" class="form-control" id="novoCategoria" aria-describedby="novoCategoriaHelp"  v-model="nomeCategoria">
                    </input-container-component>
                    
                </div>

                
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>
        </modal-component>
    </div>
</template>

<script>
import Paginate from './Paginate.vue'
    export default {
  components: { Paginate },
        computed: {
            token() {

                let token = document.cookie.split(';').find(indice => {
                    return indice.includes('token=')
                })

                token = token.split('=')[1]
                token = 'Bearer ' + token

                return token
            }
        },
        data() {
            return {
                urlBase: 'http://localhost:8000/api/v1/produto/',
                nomeMarca: '',
                nomeCategoria:'',
                arquivoImagem: [],
                transacaoStatus: '',
                transacaoDetalhes: {},
                marcas: { data: [] }
            }
        },
        methods: {
            paginacao(l) {
                if(l.url) {
                    this.urlBase = l.url //ajustando a url de consulta com o parâmetro de página
                    this.carregarLista() //requisitando novamente os dados para nossa API
                }
            },
            carregarLista() {

                let config = {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': this.token
                    }
                }

                axios.get(this.urlBase, config)
                    .then(response => {
                        this.marcas = response.data
                        //console.log(this.marcas)
                    })
                    .catch(errors => {
                        console.log(errors)
                    })
            },
            carregarImagem(e) {
                this.arquivoImagem = e.target.files
            },
            salvar() {
                console.log(this.nomeMarca, this.nomeCategoria)

                let formData = new FormData();
                formData.append('nome', this.nomeMarca)
                formData.append('categoria_id', this.nomeCategoria)

                let config = {
                    headers: {
                        //'Content-Type': 'multipart/form-data',
                        'Accept': 'application/json',
                        'Authorization': this.token
                    }
                }

                axios.post(this.urlBase, formData, config)
                    .then(response => {
                        this.transacaoStatus = 'adicionado'
                        this.transacaoDetalhes = {
                            mensagem: 'ID do registro: ' + response.data.id
                        }

                        console.log(response)
                    })
                    .catch(errors => {
                        this.transacaoStatus = 'erro'
                        this.transacaoDetalhes = {
                            mensagem: errors.response.data.message,
                            dados: errors.response.data.errors
                        }
                        //errors.response.data.message
                    })
            }
        },
        mounted() {
            this.carregarLista()
        }
    }
</script>