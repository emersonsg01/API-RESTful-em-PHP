<?php
class ParcelGenerator {
    private $valor_total;
    private $qtd_parcelas;
    private $data_primeiro_vencimento;
    private $periodicidade;
    private $valor_entrada;

    public function __construct($valor_total, $qtd_parcelas, $data_primeiro_vencimento, $periodicidade, $valor_entrada) {
        $this->valor_total = $valor_total;
        $this->qtd_parcelas = $qtd_parcelas;
        $this->data_primeiro_vencimento = $data_primeiro_vencimento;
        $this->periodicidade = $periodicidade;
        $this->valor_entrada = $valor_entrada;
    }

    public function generate() {
        $parcelas = [];
        $valor_restante = $this->valor_total - $this->valor_entrada;
        $valor_parcela = $valor_restante / $this->qtd_parcelas;
        $data_vencimento = new DateTime($this->data_primeiro_vencimento);

        if ($this->valor_entrada > 0) {
            $parcelas[] = [
                'numero' => 0,
                'data_vencimento' => (new DateTime())->format('Y-m-d'),
                'valor' => $this->valor_entrada,
                'entrada' => true
            ];
        }

        for ($i = 1; $i <= $this->qtd_parcelas; $i++) {
            $parcelas[] = [
                'numero' => $i,
                'data_vencimento' => $data_vencimento->format('Y-m-d'),
                'valor' => $valor_parcela,
                'entrada' => false
            ];
            $data_vencimento->modify($this->periodicidade === 'mensal' ? '+1 month' : '+1 week');
        }

        return [
            'total' => $this->valor_total,
            'valor_entrada' => $this->valor_entrada,
            'parcelas' => $parcelas
        ];
    }
}
?>