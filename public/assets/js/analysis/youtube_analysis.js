// function getvideoGraphConfigData(id) {
//     let rest = '';
//     let config = {
//         method: 'get',
//         maxBodyLength: Infinity,
//         url: 'http://localhost:5050/video/'+id,
//         headers: { }
//       };    
//     return axios.request(config)
//     .then(async (response) => {
//         rest = response.data
//         return await rest
//       })
// }


async function getvsVideoGraph(id){
    const labels = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec'
    ];

    // let channel1 = await getvideoGraphConfigData(id)
    // console.log(channel1)

    // const data = {
    //     labels: labels,
    //     datasets: [{
    //     label: "views",
    //     backgroundColor: '#e43834',
    //     borderColor: '#e43834',
    //     data: channel1.views_array,
    //     },{
    //     label: "Subscribers",
    //     backgroundColor: '#23a0ed',
    //     borderColor: '#23a0ed',
    //     data: channel1.subs_array,
    //     }]
    // };

    const watchers_labels = [
    'Jan',
    'Feb',
    'Mar',
    'Apr',
    'May',
    'Jun',
    'Jul',
    'Aug',
    'Sep',
    'Oct',
    'Nov',
    'Dec',
  ];

  const watchers_data = {
      labels: watchers_labels,
      datasets: [{
        label: 'Not Subscribed',
        data: [65, 59, 80, 81, 56, 55, 40,56, 55, 40,10,4],
        backgroundColor: [
          '#e4ac87'
        ],
        borderColor: [
          '#e4ac87'
        ],
        borderWidth: 1
      },
      {
        label: 'Subscribed',
        data: [65, 59, 80, 81, 56, 55, 40,56, 55, 40,10,2],
        backgroundColor: [
          '#26a12a'
        ],
        borderColor: [
          '#26a12a'
        ],
        borderWidth: 1
      }]
    };


    const config = {
        type: 'line',
        data: watchers_data,
        options: {}
    };

    const followers_config = new Chart(
        document.getElementById('views_subs_chart'),
        config
    );
}

getvsVideoGraph('kJQP7kiw5Fk')




const audience_labels = ['India', 'Germany', 'China', 'Ukraine'];
const audience_country_data = {
  labels: audience_labels,
  datasets: [{
    label: 'India',
    data: [65],
    backgroundColor: [
      '#73c3c6'
    ],
    borderColor: [
      '#73c3c6'
    ],
    borderWidth: 1
  },{
    label: 'Germany',
    data: [0, 30],
    backgroundColor: [
      '#b0225f'
    ],
    borderColor: [
      '#b0225f'
    ],
    borderWidth: 1
  },{
    label: 'China',
    data: [0, 0, 20],
    backgroundColor: [
      '#f18319'
    ],
    borderColor: [
      '#f18319'
    ],
    borderWidth: 1
  },{
    label: 'Ukraine',
    data: [0, 0, 0, 10],
    backgroundColor: [
      '#03419f'
    ],
    borderColor: [
      '#03419f'
    ],
    borderWidth: 1
  }]
};

const audience_count_config = {
    type: 'bar',
    data: audience_country_data,
    options: {}
  };

const audience_chrt = new Chart(
    document.getElementById('audience_chrt'),
    audience_count_config
  );

const labels = [
'Jan',
'Feb',
'Mar',
'Apr',
'May',
'Jun',
'Jul',
'Aug',
'Sep',
'Oct',
'Nov',
'Dec'
];

const data = {
labels: labels,
datasets: [{
  label: 'Followers',
  backgroundColor: '#e43834',
  borderColor: '#e43834',
  data: [0, 5, 4, 10, 8, 10, 15, 10, 12, 15, 20, 30],
},{
  label: 'Non Followers',
  backgroundColor: '#23a0ed',
  borderColor: '#23a0ed',
  data: [20, 25, 20, 25, 30, 35, 40, 35, 30, 25, 20, 22],
}]
};

const config = {
type: 'line',
data: data,
options: {}
};

const followers_config = new Chart(
document.getElementById('followers_chart'),
config
);

const watchers_labels = [
'Jan',
'Feb',
'Mar',
'Apr',
'May',
'Jun',
'Jul',
'Aug',
'Sep',
'Oct',
'Nov',
'Dec',
];

const watchers_data = {
  labels: watchers_labels,
  datasets: [{
    label: 'Not Subscribed',
    data: [65, 59, 80, 81, 56, 55, 40,56, 55, 40,10,4],
    backgroundColor: [
      '#e4ac87'
    ],
    borderColor: [
      '#e4ac87'
    ],
    borderWidth: 1
  },
  {
    label: 'Subscribed',
    data: [65, 59, 80, 81, 56, 55, 40,56, 55, 40,10,2],
    backgroundColor: [
      '#26a12a'
    ],
    borderColor: [
      '#26a12a'
    ],
    borderWidth: 1
  }]
};


const watchers_config = {
type: 'bar',
data: watchers_data,
options: {}
};

const watchers_chart = new Chart(
document.getElementById('watchers_chart'),
watchers_config
);


const audience_data = {
   labels: [
    'Red',
    'Blue',
    'Yellow'
  ],
  datasets: [{
    label: 'My First Dataset',
    data: [300, 50, 100],
    backgroundColor: [
      'rgb(255, 99, 132)',
      'rgb(54, 162, 235)',
      'rgb(255, 205, 86)'
    ],
    hoverOffset: 4
  }]
};

const audience_config = {
  type: 'doughnut',
  data: audience_data,
};
const audience_chart = new Chart(
document.getElementById('audience_chart'),
audience_config
);


