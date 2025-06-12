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
                                <label class="form-label">Título Profissional</label>
                                <input type="text" class="form-input" name="profissao" value="{{ old('profissao', $user->profissao) }}">
                                <span class="help-text">Título que aparece no perfil</span>
                            </div>

                            <div class="form-col">
                                <label class="form-label">Trabalho em:</label>
                                <input type="text" class="form-input" name="trab_atual" value="{{ old('trab_atual', $user->trab_atual ?? 'Desempregado') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Sobre Mim</label>
                            <textarea name="bio" class="form-textarea" maxlength="500">{{ old('bio', $user->bio) }}</textarea>
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
                                <div class="skill-tag" data-skill-id="{{ $skill->id }}">
                                    {{ $skill->nome }}
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
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Marcar as habilidades já selecionadas
    const userSkills = Array.from(document.querySelectorAll('.skill-tag')).map(tag => tag.textContent.trim());
    document.querySelectorAll('.skill-item').forEach(item => {
        if (userSkills.includes(item.textContent.trim())) {
            item.classList.add('selected');
        }
    });

    // Clique nas habilidades predefinidas
    document.querySelectorAll('.skill-item').forEach(item => {
        item.addEventListener('click', function() {
            const skillName = this.textContent.trim();
            const isSelected = this.classList.contains('selected');
            if (!isSelected) {
                // Adicionar habilidade
                fetch('/perfil/habilidade', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nome: skillName,
                        nivel: 'Intermediário'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.classList.add('selected');
                        // Adicionar visualmente na lista de skills do usuário
                        const container = document.getElementById('skillsContainer');
                        const skillTag = document.createElement('div');
                        skillTag.className = 'skill-tag';
                        skillTag.setAttribute('data-skill-id', data.skill.id);
                        skillTag.textContent = skillName;
                        container.appendChild(skillTag);
                    }
                });
            } else {
                // Remover habilidade
                // Descobrir o ID da skill (buscando no DOM)
                const skillTag = Array.from(document.querySelectorAll('.skill-tag')).find(tag => tag.textContent.trim() === skillName);
                if (skillTag) {
                    const skillId = skillTag.getAttribute('data-skill-id');
                    fetch(`/perfil/habilidade/${skillId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.classList.remove('selected');
                            skillTag.remove();
                        }
                    });
                }
            }
        });
    });

    // Adicionar habilidade personalizada ao pressionar Enter
    document.getElementById('skillInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addSkill();
        }
    });

    // Função para adicionar habilidade personalizada
    window.addSkill = function() {
        const input = document.getElementById('skillInput');
        const skillName = input.value.trim();
        if (skillName) {
            fetch('/perfil/habilidade', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    nome: skillName,
                    nivel: 'Intermediário'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const container = document.getElementById('skillsContainer');
                    const skillTag = document.createElement('div');
                    skillTag.className = 'skill-tag';
                    skillTag.setAttribute('data-skill-id', data.skill.id);
                    skillTag.textContent = skillName;
                    container.appendChild(skillTag);
                    input.value = '';
                }
            });
        }
    }
});

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

