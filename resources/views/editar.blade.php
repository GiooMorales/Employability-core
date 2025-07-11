<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/editar.css') }}">
    <title>Document</title>
</head>
<body>
@extends('layouts.app')

@section('content')

        <div class="main-content">
            <div class="edit-profile-container">
                <div class="edit-profile-header">
                    <h1 class="edit-profile-title">Editar Perfil</h1>
                    <a href="{{ route('perfil') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Voltar ao perfil
                    </a>
                </div>

                <form id="editProfileForm" method="POST" action="{{ route('perfil.atualizar') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Imagens -->
                    <div class="form-card">
                        <div class="section-header">Foto de Perfil</div>
                        <div class="profile-images">
                            <div class="profile-avatar-edit">
                                <img src="{{ $user->url_foto ? asset('storage/' . $user->url_foto) : asset('images/default-avatar.png') }}" 
                                     alt="Avatar" 
                                     class="avatar-preview" 
                                     id="avatarPreview">
                                <div class="image-upload-container">
                                    <label class="upload-btn">
                                        <i class="fas fa-upload"></i> Enviar nova foto
                                        <input type="file" 
                                               name="url_foto" 
                                               id="url_foto" 
                                               accept="image/*" 
                                               hidden
                                               onchange="previewImage(this)">
                                    </label>
                                    @if($user->url_foto)
                                    <button type="button" class="remove-btn" onclick="removePhoto()">
                                        <i class="fas fa-trash"></i> Remover foto
                                    </button>
                                    @endif
                                    <span class="help-text">Formatos aceitos: JPG, PNG. Tamanho máximo: 2MB</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informações Básicas -->
                    <div class="form-card">
                        <div class="section-header">Informações Básicas</div>

                        <div class="form-group">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" name="nome" class="form-input" value="{{ old('nome', $user->nome) }}">
                        </div>

                        <div class="form-row">
                            <div class="form-col">
                                <label class="form-label">Título do Perfil</label>
                                <input type="text" class="form-input" name="titulo" value="{{ old('titulo', $user->titulo) }}">
                                <span class="help-text">Título que aparece no perfil</span>
                            </div>

                            <div class="form-col">
                                <label class="form-label">Trabalho em:</label>
                                <input type="text" class="form-input" name="trab_atual" value="{{ old('trab_atual', $user->trab_atual ?? 'Desempregado') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Sobre Mim</label>
                            <textarea name="sobre" class="form-textarea" maxlength="500">{{ old('sobre', $user->sobre) }}</textarea>
                            <span class="help-text">Máximo de 500 caracteres</span>
                        </div>

                        <div class="form-row">
                            <div class="form-col">
                                <label class="form-label">Localização</label>
                                <input type="text" class="form-input" name="cidade" value="{{ old('localizacao', $user->cidade) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-col">
                                <label class="form-label">Link Pessoal</label>
                                <input type="url" class="form-input" name="link" value="{{ old('link', $user->link) }}" placeholder="https://">
                                <span class="help-text">Adicione um link para seu portfólio ou site pessoal</span>
                            </div>
                        </div>
                    </div>

                    <!-- Habilidades -->
                    <div class="form-card">
                        <div class="section-header">Habilidades</div>
                        <p class="help-text">Selecione suas principais habilidades técnicas e soft skills</p>
                        
                        <div class="skills-container" id="skillsContainer">
                            @foreach($user->skills as $skill)
                                @php
                                    $softSkills = ['Comunicação', 'Trabalho em Equipe', 'Liderança', 'Resolução de Problemas', 'Gestão de Tempo', 'Adaptabilidade', 'Criatividade', 'Pensamento Crítico'];
                                    $isSoftSkill = in_array($skill->nome, $softSkills);
                                @endphp
                                <div class="skill-tag" data-skill-id="{{ $skill->id }}">
                                    {{ $skill->nome }}
                                    @if(!$isSoftSkill)
                                        <span class="skill-level">{{ $skill->nivel }}</span>
                                    @endif
                                    <span class="remove-skill" onclick="removeSkill({{ $skill->id }})">
                                        <i class="fas fa-times"></i>
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="predefined-skills">
                            <h4 class="skills-subtitle">Habilidades Técnicas</h4>
                            <div class="skills-grid">
                                <div class="skill-item">JavaScript</div>
                                <div class="skill-item">Python</div>
                                <div class="skill-item">Java</div>
                                <div class="skill-item">PHP</div>
                                <div class="skill-item">C#</div>
                                <div class="skill-item">React</div>
                                <div class="skill-item">Angular</div>
                                <div class="skill-item">Vue.js</div>
                                <div class="skill-item">Node.js</div>
                                <div class="skill-item">SQL</div>
                                <div class="skill-item">MongoDB</div>
                                <div class="skill-item">Git</div>
                                <div class="skill-item">Docker</div>
                                <div class="skill-item">AWS</div>
                                <div class="skill-item">DevOps</div>
                            </div>

                            <h4 class="skills-subtitle">Soft Skills</h4>
                            <div class="skills-grid">
                                <div class="skill-item">Comunicação</div>
                                <div class="skill-item">Trabalho em Equipe</div>
                                <div class="skill-item">Liderança</div>
                                <div class="skill-item">Resolução de Problemas</div>
                                <div class="skill-item">Gestão de Tempo</div>
                                <div class="skill-item">Adaptabilidade</div>
                                <div class="skill-item">Criatividade</div>
                                <div class="skill-item">Pensamento Crítico</div>
                            </div>
                        </div>

                        <div class="skill-input-container">
                            <input type="text" id="skillInput" class="form-input" placeholder="Ou digite uma habilidade personalizada">
                            <button type="button" class="btn btn-primary" onclick="addSkill()">Adicionar</button>
                        </div>
                    </div>

                    <!-- Experiências Profissionais -->
                    <div class="section-card">
                        <div class="section-header">
                            Experiências Profissionais
                        </div>
                        <div class="section-content">
                            <div id="experiencias-list">
                                <!-- Cards de experiências serão renderizados aqui -->
                            </div>
                            <hr style="margin: 30px 0;">
                            <div id="nova-experiencia-form" class="experience-form">
                                <h4>Adicionar Nova Experiência</h4>
                                <div class="form-row">
                                    <div class="form-col">
                                        <label>Cargo *</label>
                                        <input type="text" id="novo-cargo" class="form-input">
                                    </div>
                                    <div class="form-col">
                                        <label>Empresa *</label>
                                        <input type="text" id="novo-empresa" class="form-input">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-col">
                                        <label>Tipo de Contrato</label>
                                        <select id="novo-tipo" class="form-input">
                                            <option value="">Selecione...</option>
                                            <option value="clt">CLT</option>
                                            <option value="pj">PJ</option>
                                            <option value="freelancer">Freelancer</option>
                                            <option value="estagio">Estágio</option>
                                            <option value="trainee">Trainee</option>
                                            <option value="voluntario">Voluntário</option>
                                        </select>
                                    </div>
                                    <div class="form-col">
                                        <label>Modalidade</label>
                                        <select id="novo-modalidade" class="form-input">
                                            <option value="">Selecione...</option>
                                            <option value="presencial">Presencial</option>
                                            <option value="remoto">Remoto</option>
                                            <option value="hibrido">Híbrido</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-col">
                                        <label>Data de Início *</label>
                                        <input type="date" id="novo-data-inicio" class="form-input">
                                    </div>
                                    <div class="form-col">
                                        <label>Data de Término</label>
                                        <input type="date" id="novo-data-fim" class="form-input">
                                        <div style="margin-top: 5px;">
                                            <input type="checkbox" id="novo-atual"> <label for="novo-atual">Trabalho aqui atualmente</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Descrição das Atividades *</label>
                                    <textarea id="novo-descricao" class="form-textarea" maxlength="500"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Principais Conquistas</label>
                                    <textarea id="novo-conquistas" class="form-textarea" maxlength="300"></textarea>
                                </div>
                                <button type="button" class="btn btn-primary" onclick="adicionarExperienciaAjax()">Adicionar Experiência</button>
                            </div>
                        </div>
                    </div>

                    <!-- Formações Acadêmicas -->
                    <div class="section-card">
                        <div class="section-header">
                            Formações Acadêmicas
                        </div>
                        <div class="section-content">
                            <div id="formacoes-list">
                                <!-- Cards de formações serão renderizados aqui -->
                            </div>
                            <hr style="margin: 30px 0;">
                            <div id="nova-formacao-form" class="education-form">
                                <h4>Adicionar Nova Formação</h4>
                                <div class="form-row">
                                    <div class="form-col">
                                        <label>Curso *</label>
                                        <input type="text" id="novo-curso" class="form-input">
                                    </div>
                                    <div class="form-col">
                                        <label>Universidade *</label>
                                        <div style="position: relative;">
                                            <input type="text" id="filtro-universidade" class="form-input" placeholder="Digite para buscar ou selecionar...">
                                            <ul id="universidade-sugestoes" class="autocomplete-list" style="position: absolute; z-index: 10; background: #fff; border: 1px solid #ccc; width: 100%; display: none; max-height: 180px; overflow-y: auto;"></ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-col">
                                        <label>Nível</label>
                                        <select id="novo-nivel" class="form-input">
                                            <option value="">Selecione...</option>
                                            <option value="Graduação">Graduação</option>
                                            <option value="Pós-graduação">Pós-graduação</option>
                                            <option value="Mestrado">Mestrado</option>
                                            <option value="Doutorado">Doutorado</option>
                                            <option value="Técnico">Técnico</option>
                                            <option value="Outro">Outro</option>
                                        </select>
                                    </div>
                                    <div class="form-col">
                                        <label>Situação</label>
                                        <select id="novo-situacao" class="form-input">
                                            <option value="">Selecione...</option>
                                            <option value="Cursando">Cursando</option>
                                            <option value="Concluído">Concluído</option>
                                            <option value="Trancado">Trancado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-col">
                                        <label>Data de Início *</label>
                                        <input type="date" id="novo-data-inicio-formacao" class="form-input">
                                    </div>
                                    <div class="form-col">
                                        <label>Data de Término</label>
                                        <input type="date" id="novo-data-fim-formacao" class="form-input">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary" onclick="adicionarFormacao()">Adicionar Formação</button>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <input type="hidden" name="experiencias_json" id="experiencias_json">
                        <input type="hidden" name="formacoes_json" id="formacoes_json">
                        <a href="{{ route('perfil') }}" class="btn btn-outline">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Seleção de Nível -->
<div id="nivelModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Selecione seu nível</h3>
        <div class="nivel-options">
            <button class="nivel-btn" data-nivel="Iniciante">Iniciante</button>
            <button class="nivel-btn" data-nivel="Intermediário">Intermediário</button>
            <button class="nivel-btn" data-nivel="Avançado">Avançado</button>
        </div>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeNivelModal()">Cancelar</button>
        </div>
    </div>

    
</div>



<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
}

.nivel-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin: 20px 0;
}

.nivel-btn {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.nivel-btn:hover {
    background: #f0f0f0;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

.skill-tag {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: #f0f0f0;
    border-radius: 20px;
    margin: 4px;
}

.skill-level {
    font-size: 0.8em;
    color: #666;
    background: #e0e0e0;
    padding: 2px 8px;
    border-radius: 12px;
}

.autocomplete-list {
    list-style: none;
    margin: 0;
    padding: 0;
}
.autocomplete-list li {
    padding: 8px 12px;
    cursor: pointer;
}
.autocomplete-list li:hover {
    background: #f0f0f0;
}
</style>

@section('scripts')
<script>
    const UNIVERSIDADES_JSON_URL = "{{ asset('api/uni-facul.json') }}";
    let universidadesBR = [];
    let timeout;
</script>
<script>
let selectedSkill = null;
let contadorExperiencias = 0;
let experiencias = @json($user->experiences()->get());

function showNivelModal(skillName) {
    selectedSkill = skillName;
    document.getElementById('nivelModal').style.display = 'flex';
}

function closeNivelModal() {
    document.getElementById('nivelModal').style.display = 'none';
    selectedSkill = null;
}

document.addEventListener('DOMContentLoaded', function() {
    let formacoes = [];
    // Marcar as habilidades já selecionadas
    const userSkills = Array.from(document.querySelectorAll('.skill-tag')).map(tag => {
        const skillText = tag.textContent.trim();
        const hasLevel = tag.querySelector('.skill-level');
        
        if (hasLevel) {
            // Habilidade técnica com nível
            return {
                name: skillText.split(' ').slice(0, -1).join(' '), // Remove o nível
                level: hasLevel.textContent
            };
        } else {
            // Soft skill sem nível
            return {
                name: skillText,
                level: null
            };
        }
    });

    document.querySelectorAll('.skill-item').forEach(item => {
        const skillName = item.textContent.trim();
        const userSkill = userSkills.find(s => s.name === skillName);
        if (userSkill) {
            item.classList.add('selected');
        }
    });

    // Clique nas habilidades predefinidas
    document.querySelectorAll('.skill-item').forEach(item => {
        item.addEventListener('click', function() {
            const skillName = this.textContent.trim();
            const isSelected = this.classList.contains('selected');
            
            // Verificar se é uma soft skill (está na segunda seção)
            const isSoftSkill = this.closest('.skills-grid').previousElementSibling.textContent.includes('Soft Skills');
            
            if (!isSelected) {
                if (isSoftSkill) {
                    // Para soft skills, adicionar diretamente com nível "Básico"
                    addSkillWithLevel(skillName, 'Básico');
                } else {
                    // Para habilidades técnicas, mostrar modal de seleção de nível
                    showNivelModal(skillName);
                }
            } else {
                // Remover habilidade
                const skillTag = Array.from(document.querySelectorAll('.skill-tag'))
                    .find(tag => tag.textContent.trim().startsWith(skillName));
                if (skillTag) {
                    const skillId = skillTag.getAttribute('data-skill-id');
                    removeSkill(skillId);
                }
            }
        });
    });

    // Configurar botões de nível
    document.querySelectorAll('.nivel-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const nivel = this.getAttribute('data-nivel');
            if (selectedSkill) {
                addSkillWithLevel(selectedSkill, nivel);
                closeNivelModal();
            }
        });
    });

    // --- Autocomplete de Universidades (local) ---
    const inputFiltro = document.getElementById('filtro-universidade');
    const sugestoesUl = document.getElementById('universidade-sugestoes');

    async function carregarUniversidades(nome = '') {
        if (universidadesBR.length === 0) {
            const res = await fetch(UNIVERSIDADES_JSON_URL);
            universidadesBR = await res.json();
        }
        let lista = universidadesBR;
        if (nome) {
            const termo = nome.toLowerCase();
            lista = universidadesBR.filter(u =>
                u.name.toLowerCase().includes(termo) ||
                (u['alpha_two_code'] && u['alpha_two_code'].toLowerCase().includes(termo)) ||
                (u.domains && u.domains.some(d => d.toLowerCase().includes(termo)))
            );
        }
        // Mostra até 10 sugestões
        sugestoesUl.innerHTML = '';
        if (lista.length && nome) {
            sugestoesUl.style.display = 'block';
            lista.slice(0, 10).forEach(u => {
                const li = document.createElement('li');
                li.textContent = u.name + (u.country ? ' (' + u.country + ')' : '');
                li.addEventListener('click', function() {
                    inputFiltro.value = u.name;
                    sugestoesUl.style.display = 'none';
                });
                sugestoesUl.appendChild(li);
            });
        } else {
            sugestoesUl.style.display = 'none';
        }
        // Atualiza variável global
        window.universidadesBR = universidadesBR;
    }

    inputFiltro.addEventListener('input', e => {
        clearTimeout(timeout);
        timeout = setTimeout(() => carregarUniversidades(e.target.value), 300);
    });

    // Esconde sugestões ao clicar fora
    document.addEventListener('click', function(e) {
        if (!inputFiltro.contains(e.target) && !sugestoesUl.contains(e.target)) {
            sugestoesUl.style.display = 'none';
        }
    });
});

function addSkillWithLevel(skillName, nivel) {
    fetch('/perfil/habilidade', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            nome: skillName,
            nivel: nivel
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const skillItem = Array.from(document.querySelectorAll('.skill-item'))
                .find(item => item.textContent.trim() === skillName);
            if (skillItem) {
                skillItem.classList.add('selected');
            }
            
            const container = document.getElementById('skillsContainer');
            const skillTag = document.createElement('div');
            skillTag.className = 'skill-tag';
            skillTag.setAttribute('data-skill-id', data.skill.id);
            
            // Verificar se é uma soft skill
            const isSoftSkill = skillItem && skillItem.closest('.skills-grid').previousElementSibling.textContent.includes('Soft Skills');
            
            if (isSoftSkill) {
                // Para soft skills, não mostrar o nível
                skillTag.innerHTML = `
                    ${skillName}
                    <span class="remove-skill" onclick="removeSkill(${data.skill.id})">
                        <i class="fas fa-times"></i>
                    </span>
                `;
            } else {
                // Para habilidades técnicas, mostrar o nível
                skillTag.innerHTML = `
                    ${skillName}
                    <span class="skill-level">${nivel}</span>
                    <span class="remove-skill" onclick="removeSkill(${data.skill.id})">
                        <i class="fas fa-times"></i>
                    </span>
                `;
            }
            
            container.appendChild(skillTag);
        }
    });
}

function removeSkill(skillId) {
    fetch(`/perfil/habilidade/${skillId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const skillTag = document.querySelector(`.skill-tag[data-skill-id="${skillId}"]`);
            if (skillTag) {
                const hasLevel = skillTag.querySelector('.skill-level');
                let skillName;
                
                if (hasLevel) {
                    // Habilidade técnica com nível
                    skillName = skillTag.textContent.trim().split(' ').slice(0, -1).join(' ');
                } else {
                    // Soft skill sem nível
                    skillName = skillTag.textContent.trim();
                }
                
                const skillItem = Array.from(document.querySelectorAll('.skill-item'))
                    .find(item => item.textContent.trim() === skillName);
                if (skillItem) {
                    skillItem.classList.remove('selected');
                }
                skillTag.remove();
            }
        }
    });
}

// Função para pré-visualizar a imagem
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Função para remover a foto
function removePhoto() {
    if (confirm('Tem certeza que deseja remover sua foto de perfil?')) {
        fetch('/perfil/remover-foto', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('avatarPreview').src = '{{ asset("images/default-avatar.png") }}';
                // Remove o botão de remover foto
                const removeBtn = document.querySelector('.remove-btn');
                if (removeBtn) {
                    removeBtn.remove();
                }
            }
        });
    }
}

function adicionarExperiencia() {
    contadorExperiencias++;
    const experiencia = {
        id: contadorExperiencias,
        cargo: '',
        empresa: '',
        tipo: '',
        modalidade: '',
        dataInicio: '',
        dataFim: '',
        atual: false,
        descricao: '',
        conquistas: ''
    };
    
    experiencias.push(experiencia);
    renderizarExperiencias();
}

function removerExperiencia(id) {
    experiencias = experiencias.filter(exp => exp.id !== id);
    renderizarExperiencias();
}

function renderizarExperiencias() {
    const container = document.getElementById('experiencias-list');
    container.innerHTML = '';

    experiencias.forEach(exp => {
        const experienciaHtml = `
            <div class="experience-item" data-id="${exp.id}">
                <button class="remove-btn" onclick="removerExperiencia(${exp.id})" title="Remover experiência">×</button>
                
                <div class="experience-header">
                    <div class="experience-title">
                        ${exp.cargo || 'Nova Experiência'} ${exp.empresa ? `- ${exp.empresa}` : ''}
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label>Cargo/Função *</label>
                        <input type="text" 
                               value="${exp.cargo}" 
                               onchange="atualizarExperiencia(${exp.id}, 'cargo', this.value)"
                               placeholder="Ex: Desenvolvedor Full Stack">
                    </div>
                    <div class="form-col">
                        <label>Empresa *</label>
                        <input type="text" 
                               value="${exp.empresa}" 
                               onchange="atualizarExperiencia(${exp.id}, 'empresa', this.value)"
                               placeholder="Ex: Tech Solutions Ltda">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label>Tipo de Contrato</label>
                        <select onchange="atualizarExperiencia(${exp.id}, 'tipo', this.value)">
                            <option value="">Selecione...</option>
                            <option value="clt" ${exp.tipo === 'clt' ? 'selected' : ''}>CLT</option>
                            <option value="pj" ${exp.tipo === 'pj' ? 'selected' : ''}>PJ</option>
                            <option value="freelancer" ${exp.tipo === 'freelancer' ? 'selected' : ''}>Freelancer</option>
                            <option value="estagio" ${exp.tipo === 'estagio' ? 'selected' : ''}>Estágio</option>
                            <option value="trainee" ${exp.tipo === 'trainee' ? 'selected' : ''}>Trainee</option>
                            <option value="voluntario" ${exp.tipo === 'voluntario' ? 'selected' : ''}>Voluntário</option>
                        </select>
                    </div>
                    <div class="form-col">
                        <label>Modalidade</label>
                        <select onchange="atualizarExperiencia(${exp.id}, 'modalidade', this.value)">
                            <option value="">Selecione...</option>
                            <option value="presencial" ${exp.modalidade === 'presencial' ? 'selected' : ''}>Presencial</option>
                            <option value="remoto" ${exp.modalidade === 'remoto' ? 'selected' : ''}>Remoto</option>
                            <option value="hibrido" ${exp.modalidade === 'hibrido' ? 'selected' : ''}>Híbrido</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label>Data de Início *</label>
                        <input type="date" 
                               value="${exp.dataInicio}" 
                               onchange="atualizarExperiencia(${exp.id}, 'dataInicio', this.value)">
                    </div>
                    <div class="form-col">
                        <label>Data de Término</label>
                        <input type="date" 
                               value="${exp.dataFim}" 
                               ${exp.atual ? 'disabled' : ''}
                               onchange="atualizarExperiencia(${exp.id}, 'dataFim', this.value)">
                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="checkbox" 
                                       ${exp.atual ? 'checked' : ''}
                                       onchange="toggleAtual(${exp.id}, this.checked)">
                                <label>Trabalho aqui atualmente</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Descrição das Atividades *</label>
                    <textarea placeholder="Descreva suas principais responsabilidades e atividades realizadas..."
                              onchange="atualizarExperiencia(${exp.id}, 'descricao', this.value)">${exp.descricao}</textarea>
                    <div class="help-text">Máximo de 500 caracteres</div>
                </div>

                <div class="form-group">
                    <label>Principais Conquistas</label>
                    <textarea placeholder="Descreva suas principais conquistas e resultados alcançados..."
                              onchange="atualizarExperiencia(${exp.id}, 'conquistas', this.value)">${exp.conquistas}</textarea>
                    <div class="help-text">Máximo de 300 caracteres</div>
                </div>
            </div>
        `;
        container.innerHTML += experienciaHtml;
    });
}

function atualizarExperiencia(id, campo, valor) {
    const experiencia = experiencias.find(exp => exp.id === id);
    if (experiencia) {
        experiencia[campo] = valor;
        if (campo === 'cargo' || campo === 'empresa') {
            renderizarExperiencias(); // Re-renderizar para atualizar o título
        }
    }
}

function toggleAtual(id, atual) {
    const experiencia = experiencias.find(exp => exp.id === id);
    if (experiencia) {
        experiencia.atual = atual;
        if (atual) {
            experiencia.dataFim = '';
        }
        renderizarExperiencias();
    }
}

function salvarExperiencias() {
    // Validação básica
    const experienciasInvalidas = experiencias.filter(exp => 
        !exp.cargo || !exp.empresa || !exp.dataInicio || !exp.descricao
    );

    if (experienciasInvalidas.length > 0) {
        alert('Por favor, preencha todos os campos obrigatórios (*) de todas as experiências.');
        return;
    }

    document.addEventListener('DOMContentLoaded', function() {
    // Previne submit ao pressionar Enter nos inputs de experiência
    document.getElementById('experiencias-list').addEventListener('keydown', function(e) {
        if (e.target.tagName === 'INPUT' && e.key === 'Enter') {
            e.preventDefault();
        }

        select.addEventListener('change', function() {
    if (select.value && select.value !== 'Selecione...') {
        inputFiltro.value = select.value;
    }
}); 

    });
});

    // Simular salvamento
    console.log('Experiências salvas:', experiencias);
    alert('Experiências profissionais salvas com sucesso!');
}

// Antes de enviar o formulário, serialize as experiências
document.querySelector('form').addEventListener('submit', function(e) {
    // Converter os campos para o formato esperado pelo backend
    const experienciasConvertidas = experiencias.map(exp => ({
        cargo: exp.cargo,
        empresa: exp.empresa || exp.empresa_nome || '',
        dataInicio: exp.dataInicio || exp.data_inicio || '',
        dataFim: exp.dataFim || exp.data_fim || '',
        descricao: exp.descricao,
        tipo: exp.tipo,
        modalidade: exp.modalidade,
        conquistas: exp.conquistas,
        atual: exp.atual
    }));
    document.getElementById('experiencias_json').value = JSON.stringify(experienciasConvertidas);
    document.getElementById('formacoes_json').value = JSON.stringify(formacoes);
    console.log('Experiências enviadas:', experienciasConvertidas);
    console.log('Formações enviadas:', formacoes);
});

// Inicialização: carregar experiências do backend
document.addEventListener('DOMContentLoaded', renderizarExperienciasAjax);

// Função para adicionar experiência via AJAX
function adicionarExperienciaAjax() {
    console.log('Função adicionarExperienciaAjax chamada');
    alert('Função adicionarExperienciaAjax chamada!');
    const data = {
        cargo: document.getElementById('novo-cargo').value,
        empresa_nome: document.getElementById('novo-empresa').value,
        tipo: document.getElementById('novo-tipo').value,
        modalidade: document.getElementById('novo-modalidade').value,
        data_inicio: document.getElementById('novo-data-inicio').value,
        data_fim: document.getElementById('novo-data-fim').value,
        atual: document.getElementById('novo-atual').checked ? 1 : 0,
        descricao: document.getElementById('novo-descricao').value,
        conquistas: document.getElementById('novo-conquistas').value,
        _token: '{{ csrf_token() }}'
    };
    fetch('/perfil/experiencia', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': data._token},
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(resp => {
        if (resp.success) {
            document.getElementById('novo-cargo').value = '';
            document.getElementById('novo-empresa').value = '';
            document.getElementById('novo-tipo').value = '';
            document.getElementById('novo-modalidade').value = '';
            document.getElementById('novo-data-inicio').value = '';
            document.getElementById('novo-data-fim').value = '';
            document.getElementById('novo-atual').checked = false;
            document.getElementById('novo-descricao').value = '';
            document.getElementById('novo-conquistas').value = '';
            experiencias.push(resp.experience);
            renderizarExperienciasAjax();
        } else {
            if(resp.errors) {
                alert('Erro ao adicionar experiência: ' + Object.values(resp.errors).join('\n'));
            } else {
                alert('Erro ao adicionar experiência!');
            }
        }
    });
}

// Função para renderizar cards de experiências
function renderizarExperienciasAjax() {
    console.log('Renderizando cards', experiencias);
    const container = document.getElementById('experiencias-list');
    container.innerHTML = '';
    experiencias.forEach(exp => {
        container.innerHTML += `
        <div class=\"experience-item\" style=\"background: #f8f9fa; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); padding: 18px 20px; margin-bottom: 18px;\">\n            <div style=\"display: flex; justify-content: space-between; align-items: center;\">\n                <div>\n                    <div style=\"font-weight: bold; font-size: 18px; color: #0a66c2;\">${exp.empresa_nome}</div>\n                    <div style=\"font-size: 16px; color: #333;\">${exp.cargo}</div>\n                    <div style=\"font-size: 14px; color: #666; margin-top: 2px;\">\n                        ${exp.data_inicio ? new Date(exp.data_inicio).toLocaleDateString('pt-BR', {month: 'short', year: 'numeric'}) : ''} -\n                        ${exp.atual ? 'Presente' : (exp.data_fim ? new Date(exp.data_fim).toLocaleDateString('pt-BR', {month: 'short', year: 'numeric'}) : '---')}\n                    </div>\n                    <div style=\"margin-top: 8px; color: #444;\"><strong>Descrição:</strong> ${exp.descricao}</div>\n                    ${exp.conquistas ? `<div style='margin-top: 8px; color: #444;'><strong>Conquistas:</strong> ${exp.conquistas}</div>` : ''}\n                    <div style=\"margin-top: 8px;\">\n                        ${exp.tipo ? `<span style='background: #e0e7ef; color: #0a66c2; border-radius: 12px; padding: 4px 12px; font-size: 13px; margin-right: 5px;'>${exp.tipo}</span>` : ''}\n                        ${exp.modalidade ? `<span style='background: #e0e7ef; color: #0a66c2; border-radius: 12px; padding: 4px 12px; font-size: 13px;'>${exp.modalidade}</span>` : ''}\n                    </div>\n                </div>\n                <div>\n                    <button class=\"btn btn-secondary\" onclick=\"editarExperienciaForm(${exp.id_experiencias_profissionais})\">Editar</button>\n                    <button type=\"button\" class=\"btn btn-danger\" onclick=\"removerExperienciaAjax(${exp.id_experiencias_profissionais}, event)\">Remover</button>\n                </div>\n            </div>\n            <div id=\"editar-exp-form-${exp.id_experiencias_profissionais}\" style=\"display:none; margin-top: 15px;\"></div>\n        </div>`;
    });
}

// Função para remover experiência via AJAX
function removerExperienciaAjax(id, event) {
    if (event) event.preventDefault();
    fetch(`/perfil/experiencia/${id}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
    })
    .then(response => response.json())
    .then(resp => {
        if (resp.success) {
            console.log('Antes:', experiencias);
            experiencias = experiencias.filter(exp => exp.id_experiencias_profissionais !== id);
            console.log('Depois:', experiencias);
            renderizarExperienciasAjax();
        } else {
            alert('Erro ao remover experiência!');
        }
    });
}

// Função para exibir formulário de edição
function editarExperienciaForm(id) {
    const exp = experiencias.find(e => e.id_experiencias_profissionais === id);
    const formDiv = document.getElementById(`editar-exp-form-${id}`);
    if (!exp) return;
    formDiv.innerHTML = `
        <div class='experience-form'>
            <div class="form-row">
                <div class="form-col">
                    <label>Cargo *</label>
                    <input type="text" id="edit-cargo-${id}" class="form-input" value="${exp.cargo}">
                </div>
                <div class="form-col">
                    <label>Empresa *</label>
                    <input type="text" id="edit-empresa-${id}" class="form-input" value="${exp.empresa_nome}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label>Tipo de Contrato</label>
                    <select id="edit-tipo-${id}" class="form-input">
                        <option value="">Selecione...</option>
                        <option value="clt" ${exp.tipo === 'clt' ? 'selected' : ''}>CLT</option>
                        <option value="pj" ${exp.tipo === 'pj' ? 'selected' : ''}>PJ</option>
                        <option value="freelancer" ${exp.tipo === 'freelancer' ? 'selected' : ''}>Freelancer</option>
                        <option value="estagio" ${exp.tipo === 'estagio' ? 'selected' : ''}>Estágio</option>
                        <option value="trainee" ${exp.tipo === 'trainee' ? 'selected' : ''}>Trainee</option>
                        <option value="voluntario" ${exp.tipo === 'voluntario' ? 'selected' : ''}>Voluntário</option>
                    </select>
                </div>
                <div class="form-col">
                    <label>Modalidade</label>
                    <select id="edit-modalidade-${id}" class="form-input">
                        <option value="">Selecione...</option>
                        <option value="presencial" ${exp.modalidade === 'presencial' ? 'selected' : ''}>Presencial</option>
                        <option value="remoto" ${exp.modalidade === 'remoto' ? 'selected' : ''}>Remoto</option>
                        <option value="hibrido" ${exp.modalidade === 'hibrido' ? 'selected' : ''}>Híbrido</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label>Data de Início *</label>
                    <input type="date" id="edit-data-inicio-${id}" class="form-input" value="${exp.data_inicio}">
                </div>
                <div class="form-col">
                    <label>Data de Término</label>
                    <input type="date" id="edit-data-fim-${id}" class="form-input" value="${exp.data_fim ?? ''}">
                    <div style="margin-top: 5px;">
                        <input type="checkbox" id="edit-atual-${id}" ${exp.atual ? 'checked' : ''}> <label for="edit-atual-${id}">Trabalho aqui atualmente</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Descrição das Atividades *</label>
                <textarea id="edit-descricao-${id}" class="form-textarea" maxlength="500">${exp.descricao}</textarea>
            </div>
            <div class="form-group">
                <label>Principais Conquistas</label>
                <textarea id="edit-conquistas-${id}" class="form-textarea" maxlength="300">${exp.conquistas ?? ''}</textarea>
            </div>
            <button type="button" class="btn btn-primary" onclick="salvarEdicaoExperienciaAjax(${id})">Salvar Alterações</button>
            <button type="button" class="btn btn-secondary" onclick="formDiv.innerHTML = ''">Cancelar</button>
        </div>
    `;
    formDiv.style.display = 'block';
}

// Função para salvar edição via AJAX
function salvarEdicaoExperienciaAjax(id) {
    const data = {
        cargo: document.getElementById(`edit-cargo-${id}`).value,
        empresa_nome: document.getElementById(`edit-empresa-${id}`).value,
        tipo: document.getElementById(`edit-tipo-${id}`).value,
        modalidade: document.getElementById(`edit-modalidade-${id}`).value,
        data_inicio: document.getElementById(`edit-data-inicio-${id}`).value,
        data_fim: document.getElementById(`edit-data-fim-${id}`).value,
        atual: document.getElementById(`edit-atual-${id}`).checked ? 1 : 0,
        descricao: document.getElementById(`edit-descricao-${id}`).value,
        conquistas: document.getElementById(`edit-conquistas-${id}`).value,
        _token: '{{ csrf_token() }}'
    };
    fetch(`/perfil/experiencia/${id}`, {
        method: 'PUT',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': data._token},
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(resp => {
        if (resp.success) {
            renderizarExperienciasAjax();
        } else {
            alert('Erro ao editar experiência!');
        }
    });
}

// --- Formações Acadêmicas ---
let formacoes = [];

function renderizarFormacoes() {
    const container = document.getElementById('formacoes-list');
    container.innerHTML = '';
    formacoes.forEach((f, idx) => {
        const id = f.id_formacoes;
        const idAttr = id ? `data-id=\"${id}\"` : '';
        container.innerHTML += `
        <div class=\"education-item\" style=\"background: #f8f9fa; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); padding: 18px 20px; margin-bottom: 18px;\" ${idAttr}>
            <div style=\"display: flex; justify-content: space-between; align-items: center;\">
                <div>
                    <div style=\"font-weight: bold; font-size: 18px; color: #0a66c2;\">${f.universidade}</div>
                    <div style=\"font-size: 16px; color: #333;\">${f.curso}</div>
                    <div style=\"font-size: 14px; color: #666; margin-top: 2px;\">
                        ${f.data_inicio ? new Date(f.data_inicio).toLocaleDateString('pt-BR', {month: 'short', year: 'numeric'}) : ''} -
                        ${f.data_fim ? new Date(f.data_fim).toLocaleDateString('pt-BR', {month: 'short', year: 'numeric'}) : '---'}
                    </div>
                    <div style=\"margin-top: 8px; color: #444;\">
                        <strong>Nível:</strong> ${f.nivel || '-'} | <strong>Situação:</strong> ${f.situacao || '-'}
                    </div>
                </div>
                <div>
                    <button type=\"button\" class=\"btn btn-danger\" onclick=\"removerFormacaoAjax(${id}, event)\">Remover</button>
                </div>
            </div>
        </div>`;
    });
}

// Função para remover acentos
function removerAcentos(str) {
    return str.normalize('NFD').replace(/[0-\u036f]/g, '');
}

function adicionarFormacao() {
    const curso = document.getElementById('novo-curso').value;
    const universidade = document.getElementById('filtro-universidade').value;
    const nivel = document.getElementById('novo-nivel').value;
    const situacao = document.getElementById('novo-situacao').value;
    const data_inicio = document.getElementById('novo-data-inicio-formacao').value;
    const data_fim = document.getElementById('novo-data-fim-formacao').value;
    if (!curso || !universidade || !data_inicio) {
        alert('Preencha os campos obrigatórios: curso, universidade e data de início.');
        return;
    }
    const formacaoObj = {
        curso: curso,
        universidade: universidade,
        data_inicio: data_inicio,
        data_fim: data_fim,
        nivel: nivel,
        situacao: situacao
    };
    formacoes.push(formacaoObj);
    console.log('Formação adicionada:', formacaoObj);
    renderizarFormacoes();
    document.getElementById('novo-curso').value = '';
    document.getElementById('filtro-universidade').value = '';
    document.getElementById('novo-nivel').value = '';
    document.getElementById('novo-situacao').value = '';
    document.getElementById('novo-data-inicio-formacao').value = '';
    document.getElementById('novo-data-fim-formacao').value = '';
}

function removerFormacao(idx, id = null) {
    if (id) {
        if (!confirm('Tem certeza que deseja remover esta formação?')) return;
        fetch(`/perfil/formacao/${id}`, {
            method: 'DELETE',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        })
        .then(response => response.json())
        .then(resp => {
            if (resp.success) {
                formacoes = formacoes.filter(f => f.id_formacoes != id);
                renderizarFormacoes();
            } else {
                alert('Erro ao remover formação!');
            }
        });
    } else {
        formacoes.splice(idx, 1);
        renderizarFormacoes();
    }
}

window.formacoes = @json($user->education ?? []);
if (Array.isArray(window.formacoes)) {
    formacoes = window.formacoes.map(f => ({
        id_formacoes: f.id_formacoes,
        curso: f.curso || f.nome_curso || '',
        universidade: f.instituicao || f.universidade || '',
        data_inicio: f.data_inicio || '',
        data_fim: f.data_fim || '',
        nivel: f.nivel || '',
        situacao: f.situacao || ''
    }));
    renderizarFormacoes();
}

// Função de remoção AJAX igual experiências
function removerFormacaoAjax(id, event) {
    if (event) event.preventDefault();
    fetch(`/perfil/formacao/${id}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
    })
    .then(response => response.json())
    .then(resp => {
        if (resp.success) {
            formacoes = formacoes.filter(f => f.id_formacoes != id);
            renderizarFormacoes();
        } else {
            alert('Erro ao remover formação!');
        }
    });
}
</script>
@endsection

</body>
</html>

