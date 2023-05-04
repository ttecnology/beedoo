<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Support\Carbon;
use App\Models\Message;

class MessageControllers {
    public function post(ServerRequestInterface $request, ResponseInterface $response) {
        
        try {
            $body = json_decode($request->getBody()->getContents(), true);
    
            if (empty($body['message'])) {
                $response = $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
    
                $response->getBody()->write(json_encode(['error' => 'A mensagem é obrigatória.']));
                return $response;
            }
    
            $message = trim(strip_tags($body['message']));
    
            if (strlen($message) > 300) {
                $response = $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
    
                $response->getBody()->write(json_encode(['error' => 'A mensagem é muito longa.']));
                return $response;
            }

            $response = $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
    
            $response->getBody()->write(json_encode(['message' => $message]));

            Message::create([
                'message' => $message,
                'created_at' => Carbon::now(),
            ]);

            return $response;
        } catch (\Exception $e) {
            $response = $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);

                # TODO : enviar o erro ($e->getMessage()) para um log ou integração com sentry
            $response->getBody()->write(json_encode(['error' => "Opa, já estamos buscando uma solução!"]));
            return $response;
        }
    }

    public function get(ServerRequestInterface $request, ResponseInterface $response) {
        try {
            $queryParams = $request->getQueryParams();
            $page = isset($queryParams['page']) ? (int) $queryParams['page'] : 1;
            $search = isset($queryParams['search']) ? $queryParams['search'] : '';

            $messages = Message::query()
                ->when($search, function ($query, $search) {
                    return $query->where('message', 'like', '%' . $search . '%');
                })
                ->paginate(10, ['*'], 'page', $page);

            $pagination = [
                'page' => $messages->currentPage(),
                'total_pages' => $messages->lastPage(),
            ];

            $response = $response->withHeader('Content-Type', 'application/json');

            if($messages->isEmpty()){
                $response = $response->withStatus(404);
            }

            $response->getBody()->write(json_encode($messages));

            return $response;
        } catch (\Exception $e) {
            $response = $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
                
                # TODO : enviar o erro ($e->getMessage()) para um log ou integração com sentry
            $response->getBody()->write(json_encode(['error' => "Opa, já estamos buscando uma solução!"]));
            return $response;
        }
    }
}