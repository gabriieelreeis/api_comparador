<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SimuladorController extends Controller
{
    private $dadosSimulador;
    private $simulacao = [];

    public function simular(Request $request)
    {

        // Agora não filtramos as instituições caso não seja informado e
        // somente simulamos o valor de emprestimo por ser um campo obrigatório
        $this->carregarArquivoDadosSimulador()->simularEmprestimo($request->valor_emprestimo);

        // Verifico se foi enviado pelo usuario alguma instituição
        if(isset($request->instituicoes) && !empty($request->instituicoes)) {
            $this->filtrarInstituicao($request->instituicoes);
        }

        // Verifico se foi enviado pelo usuario algum convenio
        if(isset($request->convenios) && !empty($request->convenios)) {
            $this->filtrarConvenio($request->convenios);
        }

        // Verifico se foi enviado pelo usuario alguma parcela
        if(isset($request->parcelas) && !empty($request->parcelas)) {
            $this->filtrarParcela($request->parcelas);
        }


        // Dentro do loop, a função usort é chamada para ordenar os dados por parcelas em ordem crescente.
        foreach ($this->simulacao as $banco => $dados) {
            usort($dados, function($a, $b) {
                return $a['parcelas'] <=> $b['parcelas'];
            });
            $this->simulacao[$banco] = $dados;
        }

        return response()->json($this->simulacao);
    }

    private function carregarArquivoDadosSimulador() : self
    {
        $this->dadosSimulador = json_decode(\File::get(storage_path("app/public/simulador/taxas_instituicoes.json")));
        return $this;
    }

    private function simularEmprestimo(float $valorEmprestimo) : self
    {
        foreach ($this->dadosSimulador as $dados) {
            $this->simulacao[$dados->instituicao][] = [
                "taxa"            => $dados->taxaJuros,
                "parcelas"        => $dados->parcelas,
                "valor_parcela"    => $this->calcularValorDaParcela($valorEmprestimo, $dados->coeficiente),
                "convenio"        => $dados->convenio,
            ];
        }
        return $this;
    }

    private function calcularValorDaParcela(float $valorEmprestimo, float $coeficiente) : float
    {
        return round($valorEmprestimo * $coeficiente, 2);
    }

    private function filtrarInstituicao(array $instituicoes) : self
    {
        if (\count($instituicoes))
        {
            $arrayAux = [];
            foreach ($instituicoes AS $key => $instituicao)
            {
                if (\array_key_exists($instituicao, $this->simulacao))
                {
                     $arrayAux[$instituicao] = $this->simulacao[$instituicao];
                }
            }
            $this->simulacao = $arrayAux;
        }
        return $this;
    }

    private function filtrarConvenio(array $convenios) : self
    {
        if (!empty($convenios)) {
            $filtrado = [];
            foreach ($this->simulacao as $instituicao => $dados) {
                $filtrado[$instituicao] = array_values(array_filter($dados, function ($item) use ($convenios) {
                    return in_array($item['convenio'], $convenios);
                }));

                if (empty($filtrado[$instituicao])) {
                    unset($filtrado[$instituicao]);
                }
            }
            $this->simulacao = $filtrado;
        }
        return $this;
    }

    private function filtrarParcela(int $parcela) : self
    {
        if ($parcela <= 0) {
            return $this;
        }

        $filtrado = [];
        foreach ($this->simulacao as $instituicao => $dados) {
            $filtrado[$instituicao] = array_values(array_filter($dados, function ($item) use ($parcela) {
                return $item['parcelas'] === $parcela;
            }));

            if (empty($filtrado[$instituicao])) {
                unset($filtrado[$instituicao]);
            }
        }

        $this->simulacao = $filtrado;
        return $this;
    }
}
