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

                    <div class="form-actions">
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
</style>

@section('scripts')
<script>
let selectedSkill = null;

function showNivelModal(skillName) {
    selectedSkill = skillName;
    document.getElementById('nivelModal').style.display = 'flex';
}

function closeNivelModal() {
    document.getElementById('nivelModal').style.display = 'none';
    selectedSkill = null;
}

document.addEventListener('DOMContentLoaded', function() {
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
</script>
@endsection

</body>
</html>

