<?php
// lib/reviews.php - lógica de assuntos e revisões
require_once __DIR__ . '/functions.php';

// Lista de assuntos cadastrados
function obter_assuntos(): array {
    return ler_json('subjects.json', []);
}

// Salva lista de assuntos
function salvar_assuntos(array $assuntos): void {
    gravar_json('subjects.json', array_values($assuntos));
}

// Lista de revisões cadastradas
function obter_revisoes(): array {
    return ler_json('reviews.json', []);
}

// Salva lista de revisões
function salvar_revisoes(array $revisoes): void {
    gravar_json('reviews.json', array_values($revisoes));
}

// Encontra um assunto pelo id
function encontrar_assunto(string $id): ?array {
    foreach (obter_assuntos() as $assunto) {
        if ($assunto['id'] === $id) return $assunto;
    }
    return null;
}

// Encontra uma revisão pelo id
function encontrar_revisao(string $id): ?array {
    foreach (obter_revisoes() as $revisao) {
        if ($revisao['id'] === $id) return $revisao;
    }
    return null;
}

// Gera revisões (7, 15, 30 dias) para um assunto estudado
function gerar_revisoes_para_assunto(array $assunto): array {
    $data_estudo = $assunto['learned_at'] ?? $assunto['study_date'] ?? hoje();
    if (!$data_estudo) $data_estudo = hoje();
    $existentes = obter_revisoes();
    $novas = [];
    foreach (intervalos_revisao() as $intervalo) {
        $duplicada = false;
        foreach ($existentes as $rev) {
            if ($rev['subject_id'] === $assunto['id'] && (int)$rev['interval'] === (int)$intervalo) {
                $duplicada = true; break;
            }
        }
        if ($duplicada) continue;
        $novas[] = [
            'id' => gerar_id(),
            'subject_id' => $assunto['id'],
            'interval' => (int)$intervalo,
            'due_date' => somar_dias($data_estudo, (int)$intervalo),
            'status' => 'pending',
            'done_at' => null,
            'created_at' => date('c'),
        ];
    }
    if ($novas) {
        $todas = array_merge($existentes, $novas);
        salvar_revisoes($todas);
    }
    return $novas;
}

// Marca um assunto como estudado e gera suas revisões
function marcar_assunto_estudado(string $id, ?string $data = null): bool {
    $assuntos = obter_assuntos();
    $encontrado = false;
    foreach ($assuntos as &$assunto) {
        if ($assunto['id'] === $id) {
            $assunto['learned_at'] = $data ?: hoje();
            $assunto['status'] = 'studied';
            $encontrado = true;
            gerar_revisoes_para_assunto($assunto);
            break;
        }
    }
    unset($assunto);
    if ($encontrado) salvar_assuntos($assuntos);
    return $encontrado;
}

// Marca uma revisão como concluída
function marcar_revisao_concluida(string $id): bool {
    $revisoes = obter_revisoes();
    $encontrado = false;
    foreach ($revisoes as &$revisao) {
        if ($revisao['id'] === $id) {
            $revisao['status'] = 'done';
            $revisao['done_at'] = hoje();
            $encontrado = true;
            break;
        }
    }
    unset($revisao);
    if ($encontrado) salvar_revisoes($revisoes);
    return $encontrado;
}

// Marca uma revisão como pulada
function marcar_revisao_pulada(string $id): bool {
    $revisoes = obter_revisoes();
    $encontrado = false;
    foreach ($revisoes as &$revisao) {
        if ($revisao['id'] === $id) {
            $revisao['status'] = 'skipped';
            $encontrado = true;
            break;
        }
    }
    unset($revisao);
    if ($encontrado) salvar_revisoes($revisoes);
    return $encontrado;
}

// Revisões pendentes (vencidas ou de hoje)
function revisoes_pendentes(): array {
    $saida = [];
    foreach (obter_revisoes() as $revisao) {
        if ($revisao['status'] !== 'pending') continue;
        if ($revisao['due_date'] <= hoje()) $saida[] = $revisao;
    }
    usort($saida, fn($a, $b) => strcmp($a['due_date'], $b['due_date']));
    return $saida;
}

// Revisões dos próximos N dias
function revisoes_proximas(int $dias = 7): array {
    $limite = somar_dias(hoje(), $dias);
    $saida = [];
    foreach (obter_revisoes() as $revisao) {
        if ($revisao['status'] !== 'pending') continue;
        if ($revisao['due_date'] > hoje() && $revisao['due_date'] <= $limite) $saida[] = $revisao;
    }
    usort($saida, fn($a, $b) => strcmp($a['due_date'], $b['due_date']));
    return $saida;
}

// Importa o cronograma do arquivo data/cronograma.json
function importar_cronograma(): array {
    $bruto = ler_json('cronograma.json', []);
    $assuntos = [];
    $existentes = obter_assuntos();
    $chaves_existentes = [];
    foreach ($existentes as $assunto) $chaves_existentes[$assunto['title'] . '|' . $assunto['study_date']] = true;

    $total = 0;
    foreach ($bruto as $linha) {
        // ignora dias de revisão geral
        if (stripos($linha['type'] ?? '', 'revis') !== false) continue;
        $titulo = $linha['title'] ?? '';
        [$categoria, $aula] = separar_titulo($titulo);
        $data_estudo = $linha['date'] ?? null;
        $concluido = (bool)($linha['completed'] ?? false);
        $assunto = [
            'id' => gerar_id(),
            'title' => $titulo,
            'category' => $categoria,
            'lesson' => $aula,
            'study_date' => $data_estudo,
            'type' => $linha['type'] ?? 'Videoaula',
            'completed' => $concluido,
            'status' => $concluido ? 'studied' : 'pending_study',
            'learned_at' => $concluido ? $data_estudo : null,
            'notes' => '',
            'created_at' => date('c'),
        ];
        $chave = $assunto['title'] . '|' . $assunto['study_date'];
        if (isset($chaves_existentes[$chave])) continue;
        $existentes[] = $assunto;
        $chaves_existentes[$chave] = true;
        $total++;
    }
    salvar_assuntos($existentes);

    // gera revisões para os assuntos já concluídos
    foreach ($existentes as $assunto) {
        if ($assunto['completed'] && ($assunto['learned_at'] ?? null)) {
            gerar_revisoes_para_assunto($assunto);
        }
    }
    return ['imported' => count($bruto), 'added' => $total];
}
