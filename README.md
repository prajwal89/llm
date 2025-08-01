# LLM Module

**This project is WIP DO not you this in your app**

## Installation

composer require prajwal89/llm
php artisan vendor:publish --tag=llm-config


- Add records to env
  
```env
  ANTHROPIC_API_KEY=
  OPENAI_API_KEY=
```

## Usage

- schedule this job (every 5 min) if you are using llm batch requests `Prajwal89\Llm\Jobs\UpdateBatchAndRequestStatusesJob`  

## Instructions for adding new model

## Embeddings Usage
