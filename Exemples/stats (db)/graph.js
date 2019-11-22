/**
* @file graph.js
* @brief Définit les graphiques qui seront affichés sur la page d'index
*/

/**
* Fonction définissant le graphe représentant la répartition des attributions par opérateur qui sera affiché
*/
function showGraphRes()
{
  {
    $.post("data.php",
    {action: 'nbRes'},
    function (data)
    {
      console.log(data);
      var operateur = [];
      var nbRes = [];

      for (var i in data) {
        operateur.push(data[i].operateur);
        nbRes.push(data[i].nbRes);
      }

      var chartdata = {
        labels: operateur,
        datasets: [
          {
            label: 'Nombre de ressources',
            backgroundColor: '#49e2ff',
            borderColor: '#46d5f1',
            hoverBackgroundColor: '#CCCCCC',
            hoverBorderColor: '#666666',
            data: nbRes
          }
        ]
      };

      var html = "<table border='1|1'>";
      for (var i = 0; i < data.length; i++) {
        html+="<tr>";
        html+="<td>"+data[i].operateur+"</td>";
        html+="<td>"+data[i].nbRes+"</td>";
        html+="</tr>";

      }
      html+="</table>";
      document.getElementById("tableau").innerHTML = html;

      var graphTarget = $("#graphCanvasRes");

      var barGraph = new Chart(graphTarget, {
        type: 'bar',
        data: chartdata,
        responsive: true,
        options: {
          title: {
            display: true,
            text: "Attributions par opérateur"
          },
          pan: {
            enabled: true,
            mode: 'y'
          },
          zoom: {
            enabled: true,
            mode: 'y'
          }
        }
      });
    });
  }
}

/**
* Fonction définissant le graphe représentant la répartition annuelle des attributions qui sera affiché
*/
function showGraphAnnee()
{
  {
    $.post("data.php",
    {action: 'annee'},
    function (data)
    {
      console.log(data);
      var annee = [];
      var nbRes = [];

      for (var i in data) {
        annee.push(data[i].annee);
        nbRes.push(data[i].nbRes);
      }

      var chartdata = {
        labels: annee,
        datasets: [
          {
            label: 'Nombre de ressources',
            backgroundColor: '#49e2ff',
            borderColor: '#46d5f1',
            hoverBackgroundColor: '#CCCCCC',
            hoverBorderColor: '#666666',
            data: nbRes
          }
        ]
      };

      var html = "<table border='1|1'>";
      for (var i = 0; i < data.length; i++) {
        html+="<tr>";
        html+="<td>"+data[i].annee+"</td>";
        html+="<td>"+data[i].nbRes+"</td>";
        html+="</tr>";

      }
      html+="</table>";
      document.getElementById("tableau").innerHTML = html;

      var graphTarget = $("#graphCanvasAnnee");

      var barGraph = new Chart(graphTarget, {
        type: 'bar',
        data: chartdata,
        responsive: true,
        options: {
          title: {
            display: true,
            text: "Répartition annuelle des attributions"
          }
        }
      });
    });
  }
}

/**
* Fonction définissant le graphe représentant la répartition annuelle cumulée des attributions qui sera affiché
*/
function showGraphCumulAn()
{
  {
    $.post("data.php",
    {action: 'cumulAn'},
    function (data)
    {
      console.log(data);
      var annee = [];
      var nbRes = [];

      for (var i in data) {
        annee.push(data[i].annee);
        nbRes.push(data[i].nbRes);
      }

      var chartdata = {
        labels: annee,
        datasets: [
          {
            label: 'Nombre de ressources annuelles cumulées',
            backgroundColor: '#49e2ff',
            borderColor: '#46d5f1',
            hoverBackgroundColor: '#CCCCCC',
            hoverBorderColor: '#666666',
            data: nbRes
          }
        ]
      };

      var html = "<table border='1|1'>";
      for (var i = 0; i < data.length; i++) {
        html+="<tr>";
        html+="<td>"+data[i].annee+"</td>";
        html+="<td>"+data[i].nbRes+"</td>";
        html+="</tr>";

      }
      html+="</table>";
      document.getElementById("tableau").innerHTML = html;

      var graphTarget = $("#graphCanvasCumulAn");

      var barGraph = new Chart(graphTarget, {
        type: 'bar',
        data: chartdata,
        responsive: true,
        options: {
          title: {
            display: true,
            text: "Répartition annuelle cumulée des attributions"
          }
        }
      });
    });
  }
}

/**
* Fonction définissant le graphe représentant la répartition mensuelle des attributions qui sera affiché
*/
function showGraphMois()
{
  {
    $.post("data.php",
    {action: 'mois'},
    function (data)
    {
      console.log(data);
      var mois = [];
      var nbRes = [];

      for (var i in data) {
        mois.push(data[i].mois);
        nbRes.push(data[i].nbRes);
      }

      var chartdata = {
        labels: mois,
        datasets: [
          {
            label: 'Nombre de ressources',
            backgroundColor: [
              '#90335d',
              '#ffcc99',
              '#ccff99',
              '#9999ff',
              '#fd9bca',
              '#2d3657',
              '#cb7993',
              '#b6cae9',
              '#0a7599',
              '#101010',
              '#d7897e',
              '#fdfbfa',
            ],
            borderColor: '#46d5f1',
            hoverBackgroundColor: '#CCCCCC',
            hoverBorderColor: '#666666',
            data: nbRes
          }
        ]
      };

      var html = "<table border='1|1'>";
      for (var i = 0; i < data.length; i++) {
        html+="<tr>";
        html+="<td>"+data[i].mois+"</td>";
        html+="<td>"+data[i].nbRes+"</td>";
        html+="</tr>";

      }
      html+="</table>";
      document.getElementById("tableau").innerHTML = html;

      var graphTarget = $("#graphCanvasMois");

      var pieChart = new Chart(graphTarget, {
        type: 'pie',
        data: chartdata,
        responsive: true,
        options: {
          title: {
            display: true,
            text: "Répartition mensuelle des attributions"
          }
        }
      });
    });
  }
}

/**
* Fonction définissant le graphe représentant la répartition mensuelle cumulée des attributions qui sera affiché
*/
function showGraphCumulMois()
{
  {
    $.post("data.php",
    {action: 'cumulMois'},
    function (data)
    {
      console.log(data);
      var mois = [];
      var nbRes = [];

      for (var i in data) {
        mois.push(data[i].mois);
        nbRes.push(data[i].nbRes);
      }

      var chartdata = {
        labels: mois,
        datasets: [
          {
            label: 'Nombre de ressources mensuelles cumulées',
            backgroundColor: '#49e2ff',
            borderColor: '#46d5f1',
            hoverBackgroundColor: '#CCCCCC',
            hoverBorderColor: '#666666',
            data: nbRes
          }
        ]
      };

      var html = "<table border='1|1'>";
      for (var i = 0; i < data.length; i++) {
        html+="<tr>";
        html+="<td>"+data[i].mois+"</td>";
        html+="<td>"+data[i].nbRes+"</td>";
        html+="</tr>";

      }
      html+="</table>";
      document.getElementById("tableau").innerHTML = html;

      var graphTarget = $("#graphCanvasCumulMois");

      var barGraph = new Chart(graphTarget, {
        type: 'bar',
        data: chartdata,
        responsive: true,
        options: {
          title: {
            display: true,
            text: "Répartition mensuelle cumulée des attributions"
          }
        }
      });
    });
  }
}

/**
* Fonction définissant le graphe représentant la répartition mensuelle des 12 derniers mois des attributions qui sera affiché
*/
function showGraphDerniersMois()
{
  {
    $.post("data.php",
    {action: 'derniersMois'},
    function (data)
    {
      console.log(data);
      var mois = [];
      var nbRes = [];

      for (var i in data) {
        mois.push(data[i].mois);
        nbRes.push(data[i].nbRes);
      }

      var chartdata = {
        labels: mois,
        datasets: [
          {
            label: 'Nombre de ressources',
            backgroundColor: '#49e2ff',
            borderColor: '#46d5f1',
            hoverBackgroundColor: '#CCCCCC',
            hoverBorderColor: '#666666',
            data: nbRes
          }
        ]
      };

      var html = "<table border='1|1'>";
      for (var i = 0; i < data.length; i++) {
        html+="<tr>";
        html+="<td>"+data[i].mois+"</td>";
        html+="<td>"+data[i].nbRes+"</td>";
        html+="</tr>";

      }
      html+="</table>";
      document.getElementById("tableau").innerHTML = html;

      var graphTarget = $("#graphCanvasDerniersMois");

      var barGraph = new Chart(graphTarget, {
        type: 'bar',
        data: chartdata,
        responsive: true,
        options: {
          title: {
            display: true,
            text: "Attributions lors des 12 derniers mois"
          }
        }
      });
    });
  }
}

/**
* Fonction définissant le graphe représentant la répartition par Z des attributions qui sera affiché
*/
function showGraphZ()
{
  {
    $.post("data.php",
    {action: 'Z'},
    function (data)
    {
      console.log(data);
      var Z = [];
      var nbRes = [];

      for (var i in data) {
        Z.push(data[i].Z);
        nbRes.push(data[i].nbRes);
      }

      var chartdata = {
        labels: Z,
        datasets: [
          {
            label: 'Nombre de ressources',
            backgroundColor: '#49e2ff',
            borderColor: '#46d5f1',
            hoverBackgroundColor: '#CCCCCC',
            hoverBorderColor: '#666666',
            data: nbRes
          }
        ]
      };

      var html = "<table border='1|1'>";
      for (var i = 0; i < data.length; i++) {
        html+="<tr>";
        html+="<td>"+data[i].Z+"</td>";
        html+="<td>"+data[i].nbRes+"</td>";
        html+="</tr>";

      }
      html+="</table>";
      document.getElementById("tableau").innerHTML = html;

      var graphTarget = $("#graphCanvasZ");

      var barGraph = new Chart(graphTarget, {
        type: 'bar',
        data: chartdata,
        responsive: true,
        options: {
          title: {
            display: true,
            text: "Attributions par Z"
          }
        }
      });
    });
  }
}

/**
* Fonction définissant le graphe représentant la répartition par ZNE des attributions qui sera affiché
*/
function showGraphZNE()
{
  {
    $.post("data.php",
    {action: 'ZNE'},
    function (data)
    {
      console.log(data);
      var ZNE = [];
      var nbRes = [];

      for (var i in data) {
        ZNE.push(data[i].ZNE);
        nbRes.push(data[i].nbRes);
      }

      var chartdata = {
        labels: ZNE,
        datasets: [
          {
            label: 'Nombre de ressources',
            backgroundColor: '#49e2ff',
            borderColor: '#46d5f1',
            hoverBackgroundColor: '#CCCCCC',
            hoverBorderColor: '#666666',
            data: nbRes
          }
        ]
      };

      var html = "<table border='1|1'>";
      for (var i = 0; i < data.length; i++) {
        html+="<tr>";
        html+="<td>"+data[i].ZNE+"</td>";
        html+="<td>"+data[i].nbRes+"</td>";
        html+="</tr>";

      }
      html+="</table>";
      document.getElementById("tableau").innerHTML = html;

      var graphTarget = $("#graphCanvasZNE");

      var barGraph = new Chart(graphTarget, {
        type: 'bar',
        data: chartdata,
        responsive: true,
        options: {
          title: {
            display: true,
            text: "Attributions par ZNE"
          },
          pan: {
            enabled: true,
            mode: 'y'
          },
          zoom: {
            enabled: true,
            mode: 'y'
          }
        }
      });
    });
  }
}
